<?php

namespace App\Traits;

use App\User;
use App\Driver;

trait HandleDeviceTokens
{
    protected function getBusinessTripUsersToken($tripId, $stationId, $exclude)
    {
        $tokens = User::select('device_id')
            ->Join('business_trip_users', 'business_trip_users.user_id', '=', 'users.id')
            ->where('business_trip_users.is_absent', false);

            if ($tripId) $tokens = $tokens->where('business_trip_users.trip_id', $tripId);
            if ($stationId) $tokens = $tokens->where('business_trip_users.station_id', $stationId);
            if ($exclude) $tokens = $tokens->where('users.id', '<>', $exclude);

        return $tokens->pluck('device_id')->toArray();
    }

    protected function getUsersToken(array $userId)
    {
        return User::select('device_id')
            ->whereIn('id', $userId)
            ->pluck('device_id')->toArray();
    }

    protected function getUserToken($userId)
    {
        return User::select('device_id')
            ->find($userId)->device_id;
    }

    protected function getDriverToken($driverId)
    {
        return Driver::select('device_id')
            ->find($driverId)->device_id;
    }
}