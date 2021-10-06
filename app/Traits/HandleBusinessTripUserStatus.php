<?php

namespace App\Traits;

use App\BusinessTrip;
use App\BusinessTripSubscription;
use App\StudentSubscription;
use App\BusinessTripAttendance;

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

    protected function updateStudentStatus($trip_id, $status, $students = null)
    {
        $studentsStatus = StudentSubscription::where('trip_id', $trip_id);

        if ($students) {
            if (is_array($students)) {
                $studentsStatus->whereIn('student_id', $students);
            } else {
                $studentsStatus->where('student_id', $students);
            }
        }

        return $studentsStatus->update($status);
    }

    protected function getStudentsParents($trip_id, $students)
    {
        $subscription = StudentSubscription::where('trip_id', $trip_id);

        if (is_array($students)) {
            $subscription->whereIn('student_id', $students);
        } else {
            $subscription->where('student_id', $students);
        }

        return $subscription->pluck('user_id')->unique()->toArray();
    }
}