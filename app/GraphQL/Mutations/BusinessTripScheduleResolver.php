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

            $this->cacheFlush($args);
            
            return BusinessTripSchedule::upsert($input, ['days']);
        } catch(\Exception $e) {
            throw new CustomException('We could not able to update or even create this schedule!');
        }
    }

    protected function cacheFlush(array $args)
    {
        $tags[] = 'userTrips:'.$args['user_id'];
        $tags[] = 'userLiveTrips:'.$args['user_id'];

        Cache::tags($tags)->flush();
    }

}
