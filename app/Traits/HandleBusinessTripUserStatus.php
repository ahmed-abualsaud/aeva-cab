<?php

namespace App\Traits;

use App\BusinessTripSubscription;

trait HandleBusinessTripUserStatus
{
    protected function updateUserStatus($trip_id, $status, $users = null)
    {
        $usersStatus = BusinessTripSubscription::where('trip_id', $trip_id);

        if ($users) {
            if (is_array(($users))) {
                $usersStatus->whereIn('user_id', $users);
            } else {
                $usersStatus->where('user_id', $users);
            }
        }

        return $usersStatus->update($status);
    }
}