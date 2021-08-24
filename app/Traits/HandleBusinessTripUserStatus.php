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

    protected function updateUserStudentsStatus($trip_id, $status, $users, $students)
    {
        foreach ($users as $key => $user_id) {
            $userStudents = $this->getUserStudents($trip_id, $user_id);

            $userStudents->whereIn('student_id', $students[$key])->update($status);

            if($userStudents->count() == count($students[$key]))
                $this->updateUserStatus($trip_id, $status, $user_id);
        }
    }

    protected function shouldUpdateUserAttendance(array $args)
    {
        $userStudents = $this->getUserStudents($args['trip_id'], $args['user_id']);

        if($userStudents->count() == count($args['students']))
            return true;

        return false;
    }

    protected function updateUserAttendance(array $args)
    {
        if(BusinessTrip::findOrFail($trip_id)['type'] == 'TOSCHOOL' && array_key_exists('students', $args))
        {
            if($this->shouldUpdateUserAttendance($args)) 
                return $this->updateAttendance($args);
        }
        else return $this->updateAttendance($args);      
    }

    protected function updateUserStatusWithStudents(array $args)
    {
        if(BusinessTrip::findOrFail($trip_id)['type'] == 'TOSCHOOL' && array_key_exists('students', $args))
        {
            $this->updateUserStudentsStatus(
                $args['trip_id'], 
                ['is_absent' => $args['is_absent']], 
                $args['user_id'],
                $args['students']
            );
        }
        else {
            $this->updateUserStatus(
                $args['trip_id'], 
                ['is_absent' => $args['is_absent']], 
                $args['user_id']
            );
        }      
    }

    protected function getUserStudents($trip_id, $user_id)
    {
        return StudentSubscription::where('trip_id', $trip_id)
            ->where('user_id', $user_id);
    }

    protected function updateAttendance(array $args)
    {
        $firstArgs = collect($args)->only(['date', 'trip_id', 'user_id'])->toArray();
        $secondArgs = collect($args)->only(['is_absent', 'comment'])->toArray();
        
        return BusinessTripAttendance::updateOrCreate($firstArgs, $secondArgs);
    }
}