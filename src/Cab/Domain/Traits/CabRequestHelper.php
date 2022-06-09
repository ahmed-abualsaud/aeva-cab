<?php

namespace Aeva\Cab\Domain\Traits;

use App\Driver;
use App\CarType;
use App\Vehicle;
use App\Settings;

use Aeva\Cab\Domain\Models\CabRating;
use Aeva\Cab\Domain\Models\CabRequest;

use App\Exceptions\CustomException;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\ModelNotFoundException;


trait CabRequestHelper
{
    protected function addReferralBonus($driver_id)
    {
        $settings = $this->settings(['Referral Count', 'Referral Bonus']);
        $referral_count = $settings['Referral Count'];
        $referral_bonus = $settings['Referral Bonus'];

        $driver = Driver::find($driver_id);
        if($driver) {
            if (($driver->accepted_cab_requests - $driver->cancelled_cab_requests) == $referral_count) {
                Driver::where('id', $driver->referrer_id)->increment('balance', $referral_bonus);
            }
        }
    }

    protected function applyCancelFees($cancelled_by, $request) 
    {
        if ($request->status == 'Arrived' && $cancelled_by == 'user') {
            $this->flushCancelFees($request);
        }

        if ($request->status == 'Arrived' && $cancelled_by == 'driver') {
            if ((time() - strtotime($request->history['arrived']['at'])) >= $this->settings('Waiting Time')) {
                $this->flushCancelFees($request);
            }
        }
    }

    protected function flushCancelFees($request)
    {
        $cancel_fees = CarType::select('cancel_fees')
                ->where('name', $request->history['sending']['chosen_car_type'])
                ->first()->cancel_fees;
        
        // decrement cancel_fees from user wallet
        Driver::where('id', $request->driver_id)->increment('balance', $cancel_fees);
    }

    protected function getNearestDriversWithVehicles(array $args)
    {
        $cancelled_drivers = [];
        if (array_key_exists('cancelled_drivers', $args)) {
            $cancelled_drivers = $args['cancelled_drivers'];
        }

        $drivers = $this->getNearestDrivers($args['s_lat'], $args['s_lng'], $cancelled_drivers);

        if (!count($drivers) ) {
            throw new CustomException(__('lang.no_available_drivers'));
        }

        $vehicles = Vehicle::selectRaw('
            driver_vehicles.vehicle_id,
            driver_vehicles.driver_id,
            car_models.name car_model,
            car_types.id as car_type_id,
            car_types.name as car_type,
            car_types.min_fees,
            (car_types.base_fare  + ((car_types.distance_price * ?) / 1000) + ((car_types.duration_price * car_types.surge_factor * ?) / 60)) as price,
            vehicles.license_plate as license,
            vehicles.color,
            vehicles.photo'
            , [$args['distance'], $args['duration']])
            ->join('car_types', 'car_types.id', '=', 'vehicles.car_type_id')
            ->join('car_models', 'car_models.id', '=', 'vehicles.car_model_id')
            ->join('driver_vehicles', 'driver_vehicles.vehicle_id', '=', 'vehicles.id')
            ->whereIn('driver_vehicles.driver_id', Arr::pluck($drivers, 'driver_id'))
            ->where('driver_vehicles.active', true)
            ->get();

        foreach ($vehicles as $vehicle) {
            if ($vehicle->price < $vehicle->min_fees) {$vehicles->price = $vehicle->min_fees;}
            unset($vehicle->min_fees);
        }

        return ['drivers' => $drivers, 'vehicles' => $vehicles];
    }

    protected function calculateCosts($distance, $duration, $carTypeId, $waiting_time)
    {
        if (is_array($carTypeId)) {
            $carTypes = CarType::selectRaw(
                'id, (base_fare  + ((distance_price * ?) / 1000) + ((duration_price * surge_factor * ?) / 60)) as costs, min_fees'
                , [$distance, $duration])
                ->whereIn('id', $carTypeId)
                ->get();

            $carTypes = array_map(function (array $arr) {
                if($arr['costs'] < $arr['min_fees']) {
                    $arr['costs'] = $arr['min_fees'];
                }
                unset($arr['min_fees']);
                return $arr;
            }, $carTypes->toArray());

            return collect($carTypes)->keyBy('id')->toArray();
        }

        if ($waiting_time >= $this->settings('Waiting Time')) {
            $fees = CarType::selectRaw(
                '(base_fare  + ((distance_price * ?) / 1000) + ((duration_price * surge_factor * ?) / 60) + (waiting_fees * ? / 60)) as costs, min_fees'
                , [$distance, $duration, ($waiting_time - 299)])
                ->where('id', $carTypeId)->first();
        } else {
            $fees = CarType::selectRaw(
                '(base_fare  + ((distance_price * ?) / 1000) + ((duration_price * surge_factor * ?) / 60)) as costs, min_fees'
                , [$distance, $duration])
                ->where('id', $carTypeId)->first();
        }

        if ($fees->costs < $fees->min_fees) {return $fees->min_fees;}
        return $fees->costs;
    }

    protected function updateRequest($request, $args) 
    {
        $input = Arr::except($args, ['id', 'directive', 'cancelled_by', 'cancel_reason', 'distance', 'duration']);

        $request->update($input);

        return $request;
    }

    protected function findRequest($id) 
    {
        try {
            return CabRequest::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.request_not_found'));
        }
    }

    protected function getNearestDrivers($lat, $lng, $except = []) 
    {
        $radius = $this->settings('Search Radius');

        $drivers = Driver::selectRaw('id AS driver_id, full_name as name, phone, avatar,
            ST_Distance_Sphere(point(longitude, latitude), point(?, ?))
            as distance
            ', [$lng, $lat]
            )
            ->whereNotIn('id', $except)
            ->having('distance', '<=', $radius)
            ->where('cab_status', 'Online')
            ->orderBy('distance','asc')
            ->take(5)
            ->get();
        
        return $drivers;
    }

    protected function createCabRating($args) {
        $input = [
            'request_id' => $args['id'],
            'user_id' => $args['user_id'],
            'driver_id' => $args['driver_id'],
            'trip_time' => date('Y-m-d H:i:s')
        ];
        CabRating::create($input);
    }

    protected function isTimeValidated($args)
    {
        $occupiedPeriods = CabRequest::select('schedule_time','next_free_time')
            ->whereScheduled($args['user_id'])
            ->whereRaw('
                (? >= schedule_time AND ? < next_free_time) OR 
                (? >= schedule_time AND ? < next_free_time)
            ', [
                $args['schedule_time'], $args['schedule_time'], 
                $args['next_free_time'], $args['next_free_time']
            ])
            ->first();
        
        if($occupiedPeriods || time() > strtotime($args['schedule_time'])) {
            return false;
        }
        return true;
    }

    protected function settings($name) 
    {
        if(is_array($name)) {
            $ret = Settings::select('name', 'value')->whereIn('name', $name)->get()->keyBy('name');
            return  array_map(function (array $arr) {
                $arr = $arr['value'];
                return $arr;
            }, $ret->toArray());
        }

        $ret = Settings::select('name', 'value')->where('name', $name)->first();
        if($ret) {return $ret->value;}
        return null;
    }

    protected function sphereDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
    {
        $rad = M_PI / 180;
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin($latitudeFrom * $rad) * sin($latitudeTo * $rad) +  cos($latitudeFrom * $rad) * cos($latitudeTo * $rad) * cos($theta * $rad);
        return acos($dist) / $rad * 60 * 1853;
    }
}