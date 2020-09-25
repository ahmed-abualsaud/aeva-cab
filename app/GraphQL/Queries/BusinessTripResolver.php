<?php

namespace App\GraphQL\Queries;

use App\User;
use App\Partner;
use Carbon\Carbon;
use App\BusinessTrip;
use App\DriverVehicle;
use App\BusinessTripUser;
use App\BusinessTripStation;

class BusinessTripResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function users($_, array $args)
    {
        $status = $args['status'];

        switch($status) {
            case 'subscribed':
                $users = User::selectRaw('users.id, users.name, users.avatar, business_trip_stations.name AS station_name')
                    ->where('business_trip_users.trip_id', $args['trip_id'])
                    ->join('business_trip_users', function ($join) {
                        $join->on('users.id', '=', 'business_trip_users.user_id')
                            ->whereNotNull('subscription_verified_at');
                    })
                    ->leftJoin('business_trip_stations', 'business_trip_stations.id', '=', 'business_trip_users.station_id')
                    ->get();

                break;
            case 'notSubscribed':
                $businessTripUsers = BusinessTripUser::select('user_id')
                    ->where('trip_id', $args['trip_id'])
                    ->pluck('user_id');

                $users = User::Join('partner_users', 'partner_users.user_id', '=', 'users.id')
                    ->where('partner_users.partner_id', $args['partner_id'])
                    ->select('users.id', 'users.name', 'users.avatar')
                    ->whereNotIn('users.id', $businessTripUsers)
                    ->get();

                break;
            case 'notVerified':
                $businessTripUsers = BusinessTripUser::select('user_id')
                    ->where('trip_id', $args['trip_id'])
                    ->whereNull('subscription_verified_at')
                    ->pluck('user_id');

                $users = User::select('id', 'name', 'avatar')
                    ->whereIn('id', $businessTripUsers)
                    ->get();
                break;
        }

        return $users;
    }

    public function stationAssignedUsers($_, array $args)
    {
        $stationUsers = BusinessTripUser::where('station_id', $args['station_id'])
            ->join('users', 'users.id', '=', 'business_trip_users.user_id')
            ->select('users.id', 'users.name', 'users.avatar')
            ->get();

        return $stationUsers;
    }

    public function stationNotAssignedUsers($_, array $args)
    {
        $stationAssignedUsers = BusinessTripUser::select('user_id')
            ->where('station_id', $args['station_id'])
            ->get()->pluck('user_id');

        $stationNotAssignedUsers = User::select('users.id', 'users.name', 'users.avatar')
            ->join('partner_users', function ($join) use ($args, $stationAssignedUsers) {
                $join->on('users.id', '=', 'partner_users.user_id')
                    ->where('partner_users.partner_id', $args['partner_id'])
                    ->whereNotIn('partner_users.user_id', $stationAssignedUsers);
            })->get();

        return $stationNotAssignedUsers;
    }

    public function userSubscriptions($_, array $args)
    {
        $userSubscriptions = BusinessTrip::join('business_trip_users', 'business_trips.id', '=', 'business_trip_users.trip_id')
            ->where('business_trip_users.user_id', $args['user_id'])
            ->whereNotNull('business_trip_users.subscription_verified_at')
            ->select('business_trips.*')
            ->get();

        return $userSubscriptions;
    }

    public function userTripPartners($_, array $args)
    {
        $partners = Partner::Join('business_trips', 'business_trips.partner_id', '=', 'partners.id')
            ->join('business_trip_users', 'business_trips.id', '=', 'business_trip_users.trip_id')
            ->where('business_trip_users.user_id', $args['user_id'])
            ->whereNotNull('business_trip_users.subscription_verified_at')
            ->selectRaw('partners.*')
            ->distinct()
            ->get();

        return $partners;
    }

    public function userTrips($_, array $args)
    {
        if (!array_key_exists('day', $args)) $args['day'] = strtolower(date("l"));

        $userTrips = BusinessTrip::join('business_trip_users', 'business_trips.id', '=', 'business_trip_users.trip_id')
            ->where('business_trip_users.user_id', $args['user_id']);
        
        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            $userTrips->where('business_trips.partner_id', $args['partner_id']);
        }

        $userTrips = $userTrips->whereNotNull('business_trip_users.subscription_verified_at')
            ->whereRaw('? between start_date and end_date', [date('Y-m-d')])
            ->selectRaw('business_trips.*, '. $args['day'] .' AS time')
            ->join('business_trip_schedules', function ($join) use ($args) {
                $join->on('business_trips.id', '=', 'business_trip_schedules.trip_id')
                    ->whereNotNull($args['day']);
            })
            ->get();

        if ($userTrips->isEmpty()) return [];
        
        return $this->scheduledTrips($userTrips, $args['day']);
    }

    public function userTripsByPartner($_, array $args)
    {
        if (!array_key_exists('day', $args)) $args['day'] = strtolower(date("l"));

        $userTrips = BusinessTrip::join('business_trip_users', 'business_trips.id', '=', 'business_trip_users.trip_id')
            ->where('business_trip_users.user_id', $args['user_id'])
            ->where('business_trips.partner_id', $args['partner_id'])
            ->whereNotNull('business_trip_users.subscription_verified_at')
            ->whereRaw('? between start_date and end_date', [date('Y-m-d')])
            ->selectRaw('business_trips.*, '. $args['day'] .' AS time')
            ->join('business_trip_schedules', function ($join) use ($args) {
                $join->on('business_trips.id', '=', 'business_trip_schedules.trip_id')
                    ->whereNotNull($args['day']);
            })
            ->get();

        if ($userTrips->isEmpty()) return [];
        
        return $this->scheduledTrips($userTrips, $args['day']);
    }

    public function partnerLiveTrips($_, array $args)
    {
        return BusinessTrip::select('id', 'name')
            ->where('partner_id', $args['partner_id'])
            ->where('status', true)
            ->get();
    }

    public function driverTrips($_, array $args)
    {
        if (!array_key_exists('day', $args)) $args['day'] = strtolower(date("l"));

        $driverTrips = BusinessTrip::where('driver_id', $args['driver_id'])
            ->whereRaw('? between start_date and end_date', [date('Y-m-d')])
            ->selectRaw('business_trips.*, '. $args['day'] .' AS time')
            ->join('business_trip_schedules', function ($join) use ($args) {
                $join->on('business_trips.id', '=', 'business_trip_schedules.trip_id')
                    ->whereNotNull($args['day']);
            })
            ->get();

        if ($driverTrips->isEmpty()) return [];

        return $this->scheduledTrips($driverTrips, $args['day']);
    }

    public function userLiveTrip($_, array $args)
    {
        $liveTrip = BusinessTrip::join('business_trip_users', 'business_trips.id', '=', 'business_trip_users.trip_id')
            ->where('business_trip_users.user_id', $args['user_id'])
            ->where('status', true)
            ->first();

        if ($liveTrip) {
            return [
                "status" => true,
                "trip" => $liveTrip
            ];
        }

        return [
            "status" => false,
            "trip" => null
        ];
    }

    public function driverLiveTrip($_, array $args)
    {
        $liveTrip = DriverVehicle::select('trip_type', 'trip_id')
            ->where('driver_id', $args['driver_id'])
            ->where('status', 'RIDING')
            ->first();

        if ($liveTrip) {
            return [
                "status" => true,
                "tripType" => $liveTrip->trip_type,
                "tripID" => $liveTrip->trip_id
            ];
        }

        return [
            "status" => false,
            "tripType" => null,
            "tripID" => null
        ];
    }

    protected function scheduledTrips($trips, $day) 
    {
        $sortedTrips = array();
        $now = strtotime(now()) * 1000;
        $flagTimeMargin = 60 * 30 * 1000;

        foreach($trips as $trip) {
            $tripTimeMargin = $now - ($trip->duration * 1000);
            $dateTime = date('Y-m-d', strtotime($trip->time)) . ' ' . $trip->time;
            $tripDate = strtotime($dateTime) * 1000;
            $trip->dayName = $day;
            
            if ($tripDate > $tripTimeMargin) {
                $tripInstance = new BusinessTrip();
                $trip->date = $tripDate;
                $trip->flag = ($tripDate - $flagTimeMargin) < $now;
                $trip->isReturn = false;
                $trip->startsAt = $tripDate > $now 
                    ? Carbon::parse($dateTime)->format('h:i a') 
                    : "Now";
                $tripInstance->fill($trip->toArray());
                array_push($sortedTrips, $tripInstance);
            }

            if ($trip->return_time) {
                $dateTime = date('Y-m-d', strtotime($trip->return_time)) . ' ' . $trip->return_time;
                $tripDate = strtotime($dateTime) * 1000;
                if ($tripDate > $tripTimeMargin) {
                    $tripInstance = new BusinessTrip();
                    $trip->flag = ($tripDate - $flagTimeMargin) < $now;
                    $trip->date = $tripDate;
                    $trip->startsAt = $trip->date > $now 
                        ? Carbon::parse($dateTime)->format('h:i a') 
                        : "Now";
                    $trip->isReturn = true;
                    $tripInstance->fill($trip->toArray());
                    array_push($sortedTrips, $tripInstance);
                }
            }
        }

        usort($sortedTrips, function ($a, $b) { return ($a['date'] > $b['date']); });
        
        return $sortedTrips;
    }

    protected function getFlag($day) 
    {   
        $tripDate = Carbon::parse(date('Y-m-d') . ' ' . $day);
        $minutes = $tripDate->diffInMinutes(now());
        return ($minutes < 30) ? true : false;
    } 
}