<?php

namespace App\GraphQL\Queries;

use App\User;
use App\BusinessTripUser;
use App\BusinessTripSchedule;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class UserBusinessTripResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function businessTripSubscribedUsers($_, array $args)
    {
        $users = User::selectRaw(
                'users.id, users.name, users.avatar, users.phone, 
                station.id AS station_id, station.name AS station_name, 
                destination.id AS destination_id, destination.name AS destination_name, 
                subscription.subscription_verified_at'
            )
            ->join(
                'business_trip_users as subscription', 
                'subscription.user_id', '=', 'users.id'
            )
            ->leftJoin(
                'business_trip_stations as station', 
                'station.id', '=', 'subscription.station_id'
            )
            ->leftJoin(
                'business_trip_stations as destination', 
                'destination.id', '=', 'subscription.destination_id'
            )
            ->where('subscription.trip_id', $args['trip_id'])
            ->get();

        return $users;
    }

    public function businessTripStationUsers($_, array $args)
    {
        $users = User::select('users.id', 'users.name', 'users.avatar', 'users.phone')
            ->join('business_trip_users', 'business_trip_users.user_id', '=', 'users.id');

            if ($args['status'] == 'assigned') {
                $users = $users->where('station_id', $args['station_id'])
                    ->orWhere('destination_id', $args['station_id'])
                    ->addSelect(DB::raw('
                        (CASE 
                            WHEN station_id = '.$args['station_id'].' 
                            THEN "pickup" ELSE "dropoff"
                            END
                        ) AS station_type
                    '));
            } else {
                $users = $users->where('trip_id', $args['trip_id'])
                    ->where(function ($query) use ($args) {
                        $query->whereNull('station_id')
                            ->orWhere('station_id', '<>', $args['station_id']);
                });
            }

        return $users->get();
    }

    public function businessTripSubscribers($_, array $args)
    {
        $users = User::select('users.id', 'users.name', 'users.phone', 'users.secondary_no', 'users.avatar')
            ->join('business_trip_users', 'users.id', '=', 'business_trip_users.user_id');

        if (array_key_exists('trip_id', $args) && $args['trip_id']) {
            $users = $users->where('business_trip_users.trip_id', $args['trip_id']);
        }
        
        if (array_key_exists('station_id', $args) && $args['station_id']) {
            $users = $users->addSelect(DB::raw('
                (CASE 
                    WHEN station_id = '.$args['station_id'].' 
                    THEN "pickup" ELSE "dropoff" 
                    END
                ) AS station_type
                '))
                ->where(function ($query) use ($args) {
                    $query->where('business_trip_users.station_id', $args['station_id'])
                        ->orWhere('business_trip_users.destination_id', $args['station_id']);
                });
        }
        
        $users = $users->where('business_trip_users.is_scheduled', true)
            ->where('business_trip_users.is_absent', false)
            ->whereNotNull('business_trip_users.subscription_verified_at');

        switch($args['status']) {
            case 'PICKED_UP':
                $users = $users->where('business_trip_users.is_picked_up', true);
            break;
            case 'NOT_PICKED_UP':
                $users = $users->where('business_trip_users.is_picked_up', false);
            break;
            default:
                $users = $users;
        }
        
        return $users->get();
    }

    public function businessTripUsersStatus($_, array $args)
    {
        $users = User::selectRaw('users.id, users.name, users.phone, users.secondary_no, users.avatar, business_trip_users.is_picked_up, business_trip_users.is_absent')
            ->join('business_trip_users', 'users.id', '=', 'business_trip_users.user_id');

            if (array_key_exists('trip_id', $args) && $args['trip_id']) {
                $users = $users->where('business_trip_users.trip_id', $args['trip_id']);
            }
            
            if (array_key_exists('station_id', $args) && $args['station_id']) {
                $users = $users->where('business_trip_users.station_id', $args['station_id']);
            }

        return $users->get();
    }

    public function businessTripUserStatus($_, array $args) 
    {
        try {
            $status = BusinessTripUser::select('is_absent', 'is_picked_up')
                ->where('trip_id', $args['trip_id'])
                ->where('user_id', $args['user_id'])
                ->firstOrFail();
        } catch (\Exception $e) {
            throw new CustomException('We could not able to get the user status at this trip!');
        }

        return $status;
    }

    public function businessTripAttendance($_, array $args)
    {
        $users = User::select('users.id', 'users.name', 'users.phone', 'users.secondary_no', 'users.avatar')
            ->join('business_trip_users', 'users.id', '=', 'business_trip_users.user_id')
            ->where('business_trip_users.trip_id', $args['trip_id']);

        if (array_key_exists('date', $args) && $args['date']) {
            $users = $users->leftJoin('business_trip_attendance', function ($join) use ($args) {
                $join->on('business_trip_attendance.user_id', '=', 'users.id')
                    ->where('business_trip_attendance.trip_id', $args['trip_id'])
                    ->whereDate('business_trip_attendance.date', $args['date']);
                })
                ->addSelect('business_trip_attendance.is_absent', 'business_trip_attendance.comment');
        } else {
            $users = $users->where('business_trip_users.is_scheduled', true)
                ->where('business_trip_users.is_picked_up', false)
                ->addSelect('business_trip_users.is_absent');
        }

        return $users->get();
    }

    public function businessTripSchedule($_, array $args)
    {
        try {
            return BusinessTripSchedule::select('days')
                ->where('trip_id', $args['trip_id'])
                ->where('user_id', $args['user_id'])
                ->firstOrFail();
        } catch(\Exception $e) {
            throw new CustomException('There is no schedule for this user at this trip!');
        }
    }
}
