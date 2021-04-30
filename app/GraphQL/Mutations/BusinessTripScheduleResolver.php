<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Arr;
use App\BusinessTripSchedule;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Cache;

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

            Cache::tags('userTrips:'.$args['user_id'])->flush();
            
            return BusinessTripSchedule::upsert($input, ['days']);
        } catch(\Exception $e) {
            throw new CustomException('We could not able to update or even create this schedule!');
        }
    }
}
