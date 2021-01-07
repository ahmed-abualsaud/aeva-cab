<?php

namespace App\GraphQL\Mutations;

use App\BusinessTripAttendance;
use App\Exceptions\CustomException;

class BusinessTripAttendanceResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        try {
            $input = collect($args)->except(['directive', 'status'])->toArray();
            return BusinessTripAttendance::updateOrCreate(
                $input,
                ['status' => $args['status']]
            );
        } catch(\Exception $e) {
            throw new CustomException('We could not able to create or update an attendance record!');
        }
    }
}
