<?php

namespace App\GraphQL\Mutations;

use App\BusinessTripAttendance;
use App\Traits\HandleDeviceTokens;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Cache;
use App\Traits\HandleBusinessTripUserStatus;

class BusinessTripAttendanceResolver
{
    use HandleBusinessTripUserStatus;
    use HandleDeviceTokens;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        try {
            
            if (array_key_exists('status', $args))
                $args['is_absent'] = !$args['status'];

            if ($args['date'] === date('Y-m-d'))
                $this->updateUserStatus(
                    $args['trip_id'], 
                    ['is_absent' => $args['is_absent']], 
                    $args['user_id']
                );
            
            $firstArgs = collect($args)->only(['date', 'trip_id', 'user_id'])->toArray();
            $secondArgs = collect($args)->only(['is_absent', 'comment'])->toArray();

            $this->cacheFlush($args);
            
           return BusinessTripAttendance::updateOrCreate($firstArgs, $secondArgs);

        } catch(\Exception $e) {
            throw new CustomException('We could not able to create or update an attendance record!');
        }
    }

    protected function cacheFlush(array $args)
    {
        $tags[] = 'userTrips:'.$args['user_id'];
        $tags[] = 'userLiveTrips:'.$args['user_id'];

        Cache::tags($tags)->flush();
    }
}
