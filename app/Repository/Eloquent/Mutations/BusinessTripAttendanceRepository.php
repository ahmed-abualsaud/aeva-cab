<?php

namespace App\Repository\Eloquent\Mutations;

use App\BusinessTripAttendance;
use App\Traits\HandleDeviceTokens;
use App\Exceptions\CustomException;
use App\Traits\HandleBusinessTripUserStatus;

class BusinessTripAttendanceRepository
{
    use HandleBusinessTripUserStatus;
    use HandleDeviceTokens;

    public function create(array $args)
    {
        try {            
            if ($args['date'] === date('Y-m-d'))
            {
                if(!array_key_exists('students', $args))
                    $args['students'] = null;
                    
                $this->updateUserStatusWithStudents(
                    $args['trip_id'], 
                    ['is_absent' => $args['is_absent']], 
                    $args['user_id'],
                    $args['students']
                );
            }
                
            return $this->updateUserAttendance($args);

        } catch(\Exception $e) {
            throw new CustomException(__('lang.create_attendance_failed'));
        }
    }
}
