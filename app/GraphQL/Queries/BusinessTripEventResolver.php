<?php

namespace App\GraphQL\Queries;

use App\User;
use App\BusinessTripUser;
use App\Exceptions\CustomException;

class BusinessTripEventResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function businessTripSubscribers($_, array $args)
    {
        $users = User::select('users.id', 'users.name', 'users.phone', 'users.secondary_no', 'users.avatar')
            ->join('business_trip_users', 'users.id', '=', 'business_trip_users.user_id');

        if (array_key_exists('trip_id', $args) && $args['trip_id']) {
            $users = $users->where('business_trip_users.trip_id', $args['trip_id']);
        }
        
        if (array_key_exists('station_id', $args) && $args['station_id']) {
            $users = $users->where('business_trip_users.station_id', $args['station_id']);
        }
        
        $users = $users->where('business_trip_users.is_absent', false)
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

}
