<?php

namespace App\Repository\Eloquent\Mutations;

use App\BusinessTrip;
use App\BusinessTripAttendance;
use App\Traits\HandleDeviceTokens;
use App\Exceptions\CustomException;
use App\Traits\HandleBusinessTripUserStatus;
use App\Repository\Eloquent\BaseRepository;
use Illuminate\Support\Arr;

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
            $firstArgs = Arr::only($args, ['date', 'trip_id', 'user_id']);
            $secondArgs = Arr::only($args, ['is_absent', 'comment']);

            if(array_key_exists('students', $args))
                $secondArgs['students'] = $args['students'];
                
            if ($args['date'] === date('Y-m-d'))
            {
                if(BusinessTrip::find($args['trip_id'])['type'] === 'TOSCHOOL')
                {
                    $this->updateStudentStatus(
                        $args['trip_id'], ['is_absent' => $args['is_absent']], $args['students']
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
            return $this->model->updateOrCreate($firstArgs, $secondArgs);

        } catch(\Exception $e) {
            throw new CustomException(__('lang.create_attendance_failed'));
        }
    }
}