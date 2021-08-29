<?php

namespace App\Repository\Eloquent\Mutations;

use App\BusinessTripAttendance;
use App\Traits\HandleDeviceTokens;
use App\Exceptions\CustomException;
use App\Traits\HandleBusinessTripUserStatus;
use App\Repository\Eloquent\BaseRepository;

class BusinessTripAttendanceRepository extends BaseRepository
{
    use HandleBusinessTripUserStatus;
    use HandleDeviceTokens;

    public function __construct(BusinessTripAttendance $model)
    {
        parent::__construct($model);
    }

    public function create(array $args)
    {
        try {
            if ($args['date'] === date('Y-m-d'))
                $this->updateUserStatus(
                    $args['trip_id'], 
                    ['is_absent' => $args['is_absent']], 
                    $args['user_id']
                );
            
            $firstArgs = collect($args)->only(['date', 'trip_id', 'user_id'])->toArray();
            $secondArgs = collect($args)->only(['is_absent', 'comment'])->toArray();
            
           return $this->model->updateOrCreate($firstArgs, $secondArgs);

        } catch(\Exception $e) {
            throw new CustomException(__('lang.create_attendance_failed'));
        }
    }
}