<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Arr;
use App\BusinessTripSchedule;
use App\Exceptions\CustomException;

class BusinessTripScheduleResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function reschedule($_, array $args)
    {
        try {
            $input = Arr::except($args, ['directive']);
            $input['days'] = json_encode($input['days']);
            
            return BusinessTripSchedule::upsert($input, ['days']);
        } catch(\Exception $e) {
            throw new CustomException(__('lang.create_schedule_failed'));
        }
    }
}
