<?php

namespace Aeva\Cab\Domain\Traits;

use App\User;

trait HandleDeviceTokens
{
    protected function usersToken(array $user_id)
    {
        return User::select('device_id')
            ->whereIn('id', $user_id)
            ->pluck('device_id')->toArray();
    }

    protected function userToken($user_id)
    {
        return User::select('device_id')
            ->find($user_id)->device_id;
    }
}