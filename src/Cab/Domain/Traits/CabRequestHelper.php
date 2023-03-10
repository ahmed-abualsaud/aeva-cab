<?php

namespace Aeva\Cab\Domain\Traits;

use App\User;
use App\Driver;
use App\CarType;
use App\Vehicle;
use App\Settings;
use App\DriverLog;
use App\DriverStats;

use App\Helpers\TraceEvents;
use App\Jobs\SendPushNotification;

use Aeva\Cab\Domain\Models\CabRating;
use Aeva\Cab\Domain\Models\CabRequest;
use Aeva\Cab\Domain\Models\CabRequestTransaction;

use Aeva\Cab\Domain\Events\CabRequestStatusChanged;

use App\Exceptions\CustomException;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

use Illuminate\Database\Eloquent\ModelNotFoundException;


trait CabRequestHelper
{
    protected function updateDriverStatus($driver_id, $cab_status)
    {
        try {
            $driver = Driver::findOrFail($driver_id);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.driver_not_found'));
        }

        if (strtolower($cab_status) == 'riding') {
            return $driver->update([
                'cab_status' => $cab_status
            ]);
        }

        $activity_updated_at = date('Y-m-d H:i:s');

        $driverStats = DriverStats::where('driver_id', $driver->id)->first();

        if (strtolower($cab_status) == 'offline' && $driver->cab_status == 'Online') {
            $total_working_time = strtotime($activity_updated_at) - strtotime($driverStats->activity_updated_at);
            DriverLog::log([
                'driver_id' => $driver->id,
                'total_working_time' => ($total_working_time / 60)
            ]);

            $total_working_time = $total_working_time / 60 + $driverStats->total_working_time;
            $driverStats->update([
                'total_working_time' => $total_working_time,
                'activity_updated_at'=> $activity_updated_at
            ]);
            trace(TraceEvents::GO_OFFLINE);
            return $driver->update(['cab_status' => $cab_status]);
        }

        if (strtolower($cab_status) == 'online') {
            $driverStats->update(['activity_updated_at' => $activity_updated_at]);
            trace(TraceEvents::GO_ONLINE);
            return $driver->update(['cab_status' => $cab_status]);
        }
    }

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
            $cancel_fees = $this->settings('Cancelation Fees');
            $this->flushCancelFees($request, $cancel_fees);
            CabRequestTransaction::create([
                'user_id' => $request->user_id,
                'driver_id' => $request->driver_id,
                'request_id' => $request->id,
                'costs' => $cancel_fees,
                'payment_method' => 'Cancel Fees',
                'uuid' => Str::orderedUuid()
            ]);

            DriverStats::where('driver_id', $request->driver_id)->update([
                'wallet' => DB::raw('wallet + '.$cancel_fees),
                'earnings' => DB::raw('earnings + '.$cancel_fees)
            ]);

            DriverLog::log([
                'driver_id' => $request->driver_id,
                'wallet' => $cancel_fees,
                'earnings' => $cancel_fees
            ]);
        }

        if ($request->status == 'Arrived' && $cancelled_by == 'driver') {
            $cancel_fees = $this->settings('Cancelation Fees');
            if ((time() - strtotime($request->history['arrived']['at'])) >= $this->settings('Waiting Time')) {
                $this->flushCancelFees($request, $cancel_fees);
            }
        }
    }

    protected function flushCancelFees($request, $cancel_fees)
    {
        $this->pay([
            'user_id' => $request->user_id,
            'amount' => $cancel_fees,
            'type' => 'Aevapay User Wallet',
            'uuid' => Str::orderedUuid()
        ]);
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
            car_makes.name car_make,
            car_types.id as car_type_id,
            car_types.name as car_type,
            car_types.min_fees,
            CEILING(car_types.base_fare  + ((car_types.distance_price * ?) / 1000) + ((car_types.duration_price * car_types.surge_factor * ?) / 60)) as price,
            vehicles.license_plate as license,
            vehicles.color,
            vehicles.photo'
            , [$args['distance'], $args['duration']])
            ->join('car_types', 'car_types.id', '=', 'vehicles.car_type_id')
            ->join('car_models', 'car_models.id', '=', 'vehicles.car_model_id')
            ->join('car_makes', 'car_makes.id', '=', 'vehicles.car_make_id')
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

    protected function calculateCosts($distance, $duration, $carTypeId, $waiting_time = 0)
    {
        if (is_array($carTypeId)) {
            $carTypes = CarType::selectRaw(
                'id, CEILING(base_fare  + ((distance_price * ?) / 1000) + ((duration_price * surge_factor * ?) / 60)) as costs, min_fees'
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

        $actual_waiting_time = $this->settings('Waiting Time');
        if ($waiting_time >= $actual_waiting_time) {
            $fees = CarType::selectRaw(
                'CEILING(base_fare  + ((distance_price * ?) / 1000) + ((duration_price * surge_factor * ?) / 60) + (waiting_fees * ? / 60)) as costs, min_fees'
                , [$distance, $duration, ($waiting_time - $actual_waiting_time - 1)])
                ->where('id', $carTypeId)->first();
        } else {
            $fees = CarType::selectRaw(
                'CEILING(base_fare  + ((distance_price * ?) / 1000) + ((duration_price * surge_factor * ?) / 60)) as costs, min_fees'
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
            return CabRequest::with('promoCode:id,percentage')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.request_not_found'));
        }
    }

    protected function getNearestDrivers($lat, $lng, $except = [])
    {
        $radius = $this->settings('Search Radius');

        $drivers = Driver::selectRaw('id AS driver_id,
            full_name as name, phone, avatar, latitude, longitude,
            ST_Distance_Sphere(point(longitude, latitude), point(?, ?))
            as distance
            ', [$lng, $lat]
            )
            ->whereNotIn('id', $except)
            ->where('cab_status', 'Online')
            ->where('active_status', 'Active')
            ->where('approved', true)
            ->whereRaw('TIMESTAMPDIFF(MINUTE, location_updated_at, NOW()) <= ?', [$this->settings('Location Acceptance Period')])
            ->having('distance', '<=', $radius)
            ->orderBy('distance','asc')
            ->take(7)
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

    public function pay($args)
    {
        $url = config('custom.aevapay_server_url').config('custom.aevapay_slug_pay');
        return Http::withHeaders([
            'x-api-key' => $this->getXAPIKey($args['user_id'])
        ])
        ->post($url, [
            'user_id' => $args['user_id'],
            'amount' => $args['amount'],
            'type' => $args['type'],
            'provider_transaction_reference' => $args['uuid']
        ])
        ->throw();
    }

    public function cashout($args)
    {
        $url = config('custom.credit_go_server_url').config('custom.credit_go_slug_cashout');
        return Http::withHeaders([
            'x-access-token' => $this->getXAccessToken()
        ])
        ->post($url, [
            'referenceNumber' => $args['reference_number']
        ])
        ->throw();
    }

    public function getMissedDrivers($request, $drivers_ids)
    {
        $missed = $request->history['missing']['missed'];
        $missed[] = [
            'at' => date('Y-m-d H:i:s'),
            'by' => $drivers_ids
        ];
        return $missed;
    }

    public function calculateEstimatedRoute($s_lat, $s_lng, $d_lat, $d_lng)
    {
        $distance = 0;
        $duration = 0;
        $response = Http::get(config('custom.google_maps_url').'&%2520waypoints=optimize:true%7C&origin='.$s_lat.','.$s_lng.'&destination='.$d_lat.','.$d_lng);
        if ($response['status'] ==  'OK') {
            foreach ($response['routes'][0]['legs'] as $leg) {
                $distance += $leg['distance']['value'];
                $duration += $leg['duration']['value'];
            }
        }
        return ['distance' => $distance, 'duration' => $duration];
    }

    public function calculateRealRoute($s_lat, $s_lng, $d_lat, $d_lng, $locations)
    {
        if (!empty($locations) && gettype($locations) == 'array')
        {
            array_multisort(array_column($locations, 'id'), SORT_ASC, $locations);
            foreach ($locations as $location) {
                $path[] = $location['latitude'].','.$location['longitude'];
            }
            $locations = implode('|', $path);
        }

        $distance = 0;
        $duration = 0;
        $response = Http::get(config('custom.google_maps_url').'&waypoints='.$locations.'&origin='.$s_lat.','.$s_lng.'&destination='.$d_lat.','.$d_lng);
        if ($response['status'] ==  'OK') {
            foreach ($response['routes'][0]['legs'] as $leg) {
                $distance += $leg['distance']['value'];
                $duration += $leg['duration']['value'];
            }
        }
        return ['distance' => $distance, 'duration' => $duration];
    }

    public function updateUserWallet($user_id, $costs, $type, $uuid)
    {
        try {
            $user = User::findOrFail($user_id);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.user_not_found'));
        }

        if ($type == 'Aevacab Refund' && is_zero($costs)) { return; }

        if ($type == 'Aevapay User Wallet' && is_zero($user->wallet)) {
            throw new CustomException(__('lang.empty_user_wallet'));
        }

        if ($type == 'Aevapay User Wallet' && $user->wallet < $costs) {
            $costs = $user->wallet;
        }

        try {
            $this->pay([
                'user_id' => $user_id,
                'amount' => $costs,
                'type' => $type,
                'uuid' => $uuid
            ]);
        } catch (\Exception $e) {
            throw new CustomException($this->parseErrorMessage($e->getMessage(), 'status"'));
        }

        return $costs;
    }

    public function updateDriverWallet($driver_id, $earnings, $cash, $wallet, $costs = 0)
    {
        try {
            $stats = DriverStats::where('driver_id', $driver_id)->firstOrFail();
        } catch (\Exception $e) {
            throw new CustomException(__('lang.driver_not_found'));
        }

        if($stats->wallet + $wallet < 0) {
            throw new CustomException(__('lang.insufficient_driver_wallet_balance', ['cash_amount' => $stats->wallet + $costs]));
        }

        $stats->update([
            'cash' => DB::raw('cash + '.$cash),
            'wallet' => DB::raw('wallet + '.$wallet),
            'earnings' => DB::raw('earnings + '.$earnings)
        ]);

        DriverLog::log([
            'driver_id' => $driver_id,
            'cash' => $cash,
            'wallet' => $wallet,
            'earnings' => $earnings
        ]);
    }

    public function notifyUserOfPayment($request, $refund)
    {
        $socket_request = $request->toArray();
        $socket_request['refund'] = $refund;
        SendPushNotification::dispatch(
            $this->userToken($socket_request['user_id']),
            __('lang.ride_completed_body'),
            __('lang.ride_completed'),
            ['view' => 'RideCompleted', 'id' => $socket_request['id']]
        );

        broadcast(new CabRequestStatusChanged($socket_request));
    }

    public function parseErrorMessage($err_mesg, $needle)
    {
        $index = strpos($err_mesg, $needle);
        if($index) {
            return json_decode(substr($err_mesg, $index - 2))->message;
        }
        return $err_mesg;
    }

    protected function getXAccessToken()
    {
        $url = config('custom.credit_go_server_url').config('custom.credit_go_slug_auth');
        $response = Http::post($url, [
            'phone'=> config('custom.credit_go_phone'),
            'passcode'=> config('custom.credit_go_pass_code')
        ])
        ->throw();
        return $response['token'];
    }

    protected function getXAPIKey($input)
    {
        $server_key = config('custom.aevapay_server_key');
        $str = $server_key.$input;
        $hashed_str = hash("sha256",$str,true);
        return base64_encode($hashed_str);
    }
}
