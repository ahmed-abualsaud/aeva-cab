<?php

namespace App\Traits;

use App\User;
use App\Driver;
use App\Follower;

trait HandleDeviceTokens
{

    protected function tripUsersToken($trip_id)
    {
        return $this->getBusinessTripUsersToken()
            ->where('business_trip_users.trip_id', $trip_id)
            ->pluck('device_id')
            ->merge($this->getBusinessTripFollowersTokens($trip_id))
            ->unique()
            ->toArray();
    }

    protected function tripUsersTokenWithout($trip_id, $exclude)
    {
        return $this->getBusinessTripUsersToken()
            ->where('business_trip_users.trip_id', $trip_id)
            ->where('users.id', '<>', $exclude)
            ->pluck('device_id')
            ->toArray();
    }

    protected function stationUsersToken($station_id, $trip_id)
    {
        return $this->getBusinessTripUsersToken()
            ->where('business_trip_users.station_id', $station_id)
            ->pluck('device_id')
            ->merge($this->getBusinessTripFollowersTokens($trip_id))
            ->unique()
            ->toArray();
    }

    protected function getBusinessTripUsersToken()
    {
        return User::select('device_id')
            ->Join('business_trip_users', 'business_trip_users.user_id', '=', 'users.id')
            ->where('business_trip_users.is_absent', false)
            ->where('business_trip_users.is_scheduled', true);
    }

    protected function usersToken($trip_id, array $user_id)
    {
        return User::select('device_id')
            ->whereIn('id', $user_id)
            ->pluck('device_id')
            ->merge($this->getUsersFollowersTokens($trip_id, $user_id))
            ->unique()
            ->toArray();
    }

    protected function userToken($trip_id, $user_id)
    {
        return User::select('device_id')
            ->where('id', $user_id)
            ->pluck('device_id')
            ->merge($this->getUserFollowersTokens($trip_id, $user_id))
            ->unique()
            ->toArray();
    }

    protected function driverToken($driver_id)
    {
        return Driver::select('device_id')
            ->find($driver_id)->device_id;
    }

    protected function getBusinessTripFollowersTokens($trip_id)
    {
        return Follower::Join('users', 'users.id', '=', 'business_trip_followers.follower_id')
            ->where('trip_id', $trip_id)
            ->pluck('device_id')
            ->toArray();
    }

    protected function getUserFollowersIDs($trip_id, $user_id)
    {
        return Follower::Join('users', 'users.id', '=', 'business_trip_followers.follower_id')
            ->where('trip_id', $trip_id)
            ->where('user_id', $user_id)
            ->pluck('device_id')
            ->toArray();
    }

    protected function getUsersFollowersTokens($trip_id, array $user_id)
    {
        return Follower::Join('users', 'users.id', '=', 'business_trip_followers.follower_id')
            ->where('trip_id', $trip_id)
            ->whereIn('user_id', $user_id)
            ->pluck('device_id')
            ->toArray();
    }
}