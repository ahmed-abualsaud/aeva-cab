<?php

namespace App\GraphQL\Mutations;

use App\BusinessTripAttendance;
use App\Exceptions\CustomException;
use App\Traits\HandleBusinessTripUserStatus;

class BusinessTripAttendanceResolver
{
    use HandleBusinessTripUserStatus;
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        try {
            $required = collect($args)->only(['date', 'trip_id', 'user_id'])->toArray();
            $optional = collect($args)->only(['status', 'comment'])->toArray();

            if (array_key_exists('status', $optional)) {
                $this->updateUserStatus(
                    $args['trip_id'], 
                    ['is_absent' => !$args['status']], 
                    $args['user_id']
                );
            } else {
                $optional['status'] = null;
            }
            
            return BusinessTripAttendance::updateOrCreate($required, $optional);
        } catch(\Exception $e) {
            throw new CustomException('We could not able to create or update an attendance record!');
        }
    }
}
