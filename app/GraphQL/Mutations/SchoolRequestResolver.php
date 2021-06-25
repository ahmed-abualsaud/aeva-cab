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
            throw new CustomException(__('lang.CreateSchoolRequestFailed'));
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
            throw new CustomException(__('lang.UpdateSchoolRequestFailed'));
        }

        return $schoolRequest;
    }

    public function changeStatus($_, array $args)
    {
        try {
            $updateInput = collect($args)->only(['status', 'response'])->toArray();

            switch($args['status']) {
                case 'PENDING':
                    SchoolRequest::restore($args['requestIds']);
                    break;

                default:
                    SchoolRequest::exclude($args['requestIds'], $updateInput);
                    if (array_key_exists('notify', $args) && $args['notify'])
                        $this->notifyUsers($args);
            }
            
        } catch (\Exception $e) {
            throw new CustomException(__('lang.ChangeRequestFailed'));
        }

        return __('lang.RequestChanged');
    }

    protected function notifyUsers(array $args)
    {
        try {
               
            foreach($args['users'] as $user) {

                $responseMsg = 'Your school request # ' 
                    . $user['requestId'] . ' has been ' 
                    . strtolower($args['status']);
    
                if (array_key_exists('response', $args) && $args['response']) 
                    $responseMsg .= '. '. $args['response'];

                SendPushNotification::dispatch(
                    $this->userToken($user['userId']), 
                    $responseMsg, 
                    'Qruz to School',
                    ['view' => 'SchoolRequest', 'id' => $user['requestId']]
                );
            }
        } catch (\Exception $e) {
            //
        }
    }
    
    public function destroy($_, array $args)
    {
        try {
            SchoolRequest::whereIn('id', $args['id'])->delete();
        } catch (\Exception $e) {
            throw new CustomException(__('lang.DeleteRequestFailed'));
        }

        return __('lang.RequestDeleted');
    }
}
