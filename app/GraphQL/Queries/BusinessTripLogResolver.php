<?php

namespace App\GraphQL\Queries;

use App\User;
use App\Driver;
use App\TripLog;
use App\BusinessTripUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BusinessTripLogResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function pickedUsers($_, array $args)
    {
        $users = TripLog::where('log_id', $args['log_id'])
            ->where('status', 'PICKED_UP')
            ->join('users', 'users.id', '=', 'trip_logs.user_id')
            ->select('users.*')
            ->get();

        return $users;
    }

    public function businessTripSubscribers($_, array $args)
    {
        $users = User::select('users.id', 'users.name', 'users.phone', 'users.avatar')
            ->join('business_trip_users', 'users.id', '=', 'business_trip_users.user_id');

        if (array_key_exists('trip_id', $args) && $args['trip_id']) {
            $users = $users->where('business_trip_users.trip_id', $args['trip_id']);
        }
        
        if (array_key_exists('station_id', $args) && $args['station_id']) {
            $users = $users->where('business_trip_users.station_id', $args['station_id']);
        }
        
        $users = $users->whereNotNull('business_trip_users.subscription_verified_at');

        switch($args['status']) {
            case 'PICKED_UP':
                $users = $users->where('business_trip_users.is_picked', true);
            break;
            case 'NOT_PICKED_UP':
                $users = $users->where('business_trip_users.is_picked', false);
            break;
            default:
                $users = $users;
        }
        
        return $users->get();
    }

    public function arrivedAndPickedUsers($_, array $args)
    {
        $users = User::selectRaw('users.id, users.name, users.phone, users.avatar, 
                (CASE WHEN is_picked = 1 THEN 1 ELSE 0 END) AS is_picked_up, 
                (CASE WHEN is_arrived = 1 THEN 1 ELSE 0 END) AS is_arrived
            ')
            ->join('business_trip_users', 'users.id', '=', 'business_trip_users.user_id');

            if (array_key_exists('trip_id', $args) && $args['trip_id']) {
                $users = $users->where('business_trip_users.trip_id', $args['trip_id']);
            }
            
            if (array_key_exists('station_id', $args) && $args['station_id']) {
                $users = $users->where('business_trip_users.station_id', $args['station_id']);
            }

        return $users->get();
    }

    public function show($_, array $args)
    {
        $log = TripLog::selectRaw('trip_logs.status, trip_logs.latitude, trip_logs.longitude, trip_logs.created_at, users.name as user');

            if (array_key_exists('user_id', $args) && $args['user_id']) {
                $log = $log->join('users', function ($join) use ($args) {
                    $join->on('users.id', '=', 'trip_logs.user_id')
                        ->where('trip_logs.user_id', $args['user_id']);
                });
            } else {
                $log = $log->leftJoin('users', 'users.id', '=', 'trip_logs.user_id');
            }

            
            $log = $log->where('log_id', $args['log_id'])
                ->orderBy('trip_logs.created_at')
                ->get();

        return $log; 
    }

    public function driverLocation($_, array $args)
    {
        try {
            $location = Driver::select(['latitude', 'longitude'])
                ->findOrFail($args['driver_id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('No data for the provided driver ID');
        }

        return [
            'latitude' => $location->latitude,
            'longitude' => $location->longitude
        ];
    }

}
