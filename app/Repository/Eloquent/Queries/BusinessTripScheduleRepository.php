<?php

namespace App\Repository\Eloquent\Queries;   

use App\BusinessTripSchedule;
use App\Exceptions\CustomException;
use App\Repository\Queries\MainRepositoryInterface;

class BusinessTripScheduleRepository extends BaseRepository implements MainRepositoryInterface
{

   public function __construct(BusinessTripSchedule $model)
   {
        parent::__construct($model);
   }

   public function invoke(array $args)
   {
        try {
            return $this->model->select('days')
                ->where('trip_id', $args['trip_id'])
                ->where('user_id', $args['user_id'])
                ->firstOrFail();
        } catch(\Exception $e) {
            throw new CustomException(__('lang.no_schedule'));
        }
   }
}