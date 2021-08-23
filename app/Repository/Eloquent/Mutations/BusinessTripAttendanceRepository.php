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
                $this->updateUserStatusWithStudents($args);
                
            return $this->updateUserAttendance($args);

        } catch(\Exception $e) {
            throw new CustomException(__('lang.create_attendance_failed'));
        }
    }
}
