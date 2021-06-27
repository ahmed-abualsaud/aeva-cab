<?php

namespace App\GraphQL\Queries;

use App\BusinessTripSchedule;
use App\Exceptions\CustomException;

class BusinessTripScheduleResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        try {
            return BusinessTripSchedule::select('days')
                ->where('trip_id', $args['trip_id'])
                ->where('user_id', $args['user_id'])
                ->firstOrFail();
        } catch(\Exception $e) {
            throw new CustomException(__('lang.no_schedule'));
        }
    }
}
