<?php

namespace App\Traits;

use App\BusinessTripSubscription;
use App\StudentSubscription;

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

    protected function updateUserStudentsStatus($trip_id, $user_id, $students, $status)
    {
        $userStudents = StudentSubscription::where('trip_id', $trip_id)
            ->where('user_id', $user_id);

        $userStudents->whereIn('student_id', $students)->update($status);

        if($userStudents->count() == count($students))
            return true;

        return false;
    }
}