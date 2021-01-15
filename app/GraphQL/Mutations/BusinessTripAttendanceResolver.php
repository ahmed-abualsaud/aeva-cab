<?php

namespace App\GraphQL\Mutations;

use App\BusinessTripAttendance;
use App\Jobs\SendPushNotification;
use App\Traits\HandleDeviceTokens;
use App\Exceptions\CustomException;
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

            $firstArgs = collect($args)->only(['date', 'trip_id', 'user_id'])->toArray();
            $secondArgs = collect($args)->only(['is_absent', 'comment'])->toArray();

            $this->updateUserStatus(
                $args['trip_id'], 
                ['is_absent' => $args['is_absent']], 
                $args['user_id']
            );

            if (array_key_exists('trip_name', $args)) {
                $status_text = $args['is_absent'] ? 'Absent' : 'Present';
                $token = $this->getUserToken($args['user_id']);
                $title = $args['trip_name'] . ' Trip';
                $msg = 'The trip captain has changed your attendance status to '.$status_text.', If this isn\'t the case, you could revert it back from inside the trip.';
                
                SendPushNotification::dispatch($token, $msg, $title);
            }
            
            BusinessTripAttendance::updateOrCreate($firstArgs, $secondArgs);

        } catch(\Exception $e) {
            throw new CustomException('We could not able to create or update an attendance record!');
        }

        return "Business trip attendance has been saved successfully";
    }
}
