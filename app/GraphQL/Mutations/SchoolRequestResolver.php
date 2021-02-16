<?php

namespace App\GraphQL\Mutations;

use App\User;
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

    public function create($_, array $args)
    {
        try {
            $input = collect($args)->except(['directive'])->toArray();

            User::updateSecondaryNumber($args['contact_phone']);

            $schoolRequest = SchoolRequest::create($input);
        } catch (\Exception $e) {
            throw new CustomException('We could not able to create this school request!');
        }

        return $schoolRequest;
    }

    public function update($_, array $args)
    {
        try {
            $input = collect($args)->except(['id', 'directive'])->toArray();
            $schoolRequest = SchoolRequest::findOrFail($args['id']);

            if (array_key_exists('contact_phone', $args) && $args['contact_phone'])
                User::updateSecondaryNumber($args['contact_phone']);
    
            $schoolRequest->update($input);
        } catch (\Exception $e) {
            throw new CustomException('We could not able to update this school request!');
        }

        return $schoolRequest;
    }

    public function reject($_, array $args)
    {
        try {
            SchoolRequest::reject($args['requestIds'], $args['response']);
            
            if ($args['notify']) {
                SendPushNotification::dispatch(
                    $this->getUsersToken($args['userIds']),
                    'Your request has been rejected! '. $args['response'],
                    'Qruz to School'
                );
            }

        } catch (\Exception $e) {
            throw new CustomException('We could not able to reject selected requests!');
        }

        return "Selected requests have been rejected";
    }

    public function restore($_, array $args)
    {
        try {
            SchoolRequest::restore($args['id']);
        } catch (\Exception $e) {
            throw new CustomException('We could not able to restore selected requests!');
        }

        return "Selected requests have been restored";
    }
    
    public function destroy($_, array $args)
    {
        try {
            SchoolRequest::whereIn('id', $args['id'])->delete();
        } catch (\Exception $e) {
            throw new CustomException('We could not able to delete selected requests!');
        }

        return "Selected requests have been deleted";
    }

    protected function updateSecondaryNumber($no)
    {
        try {
            auth('user')
                ->user()
                ->update(['secondary_no' => $no]);
        } catch (\Exception $e) {
            //
        }
    }
}
