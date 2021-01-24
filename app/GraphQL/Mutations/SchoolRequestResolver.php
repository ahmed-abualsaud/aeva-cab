<?php

namespace App\GraphQL\Mutations;

use App\SchoolRequest;
use App\Jobs\SendPushNotification;
use App\Traits\HandleDeviceTokens;
use App\Exceptions\CustomException;

class SchoolRequestResolver
{
    use HandleDeviceTokens;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function reject($_, array $args)
    {
        try {
            SchoolRequest::reject($args['requestIds'], $args['response']);
            
            SendPushNotification::dispatch(
                $this->getUsersToken($args['userIds']),
                'Your request has been rejected! '. $args['response'],
                'Qruz to School'
            );

        } catch (\Exception $e) {
            throw new CustomException('We could not able to reject the selected requests');
        }

        return "Selected requests have been rejected";
    }
    
    public function destroy($_, array $args)
    {
        return SchoolRequest::whereIn('id', $args['id'])->delete();
    }
}
