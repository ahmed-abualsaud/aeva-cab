<?php

namespace App\Repository\Eloquent\Mutations;

use Illuminate\Support\Arr;
use App\BusinessTripSchedule;
use App\Exceptions\CustomException;
use App\Repository\Eloquent\BaseRepository;
use App\Repository\Mutations\BusinessTripScheduleRepositoryInterface;

class BusinessTripScheduleRepository extends BaseRepository implements BusinessTripScheduleRepositoryInterface
{
    public function __construct(BusinessTripSchedule $model)
    {
        parent::__construct($model);
    }
    
    public function reschedule(array $args)
    {
        try {
            $input = Arr::except($args, ['directive']);
            $input['days'] = json_encode($input['days']);
            
            return $this->model->upsert($input, ['days']);
        } catch(\Exception $e) {
            throw new CustomException(__('lang.create_schedule_failed'));
        }
    }
}
