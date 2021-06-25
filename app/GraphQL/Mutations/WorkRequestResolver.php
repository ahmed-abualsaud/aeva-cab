<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\WorkRequest;
use App\Jobs\SendPushNotification;
use App\Traits\HandleDeviceTokens;
use App\Exceptions\CustomException;

class WorkRequestResolver
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

            if (array_key_exists('contact_phone', $args) && $args['contact_phone'])
                User::updateSecondaryNumber($args['contact_phone']);

            $workRequest = WorkRequest::create($input);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.CreateWorkplaceFailed'));
        }

        return $workRequest;
    }

    public function update($_, array $args)
    {
        try {
            $input = collect($args)->except(['id', 'directive'])->toArray();
            $workRequest = WorkRequest::findOrFail($args['id']);

            if (array_key_exists('contact_phone', $args) && $args['contact_phone'])
                User::updateSecondaryNumber($args['contact_phone']);
    
            $workRequest->update($input);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.UpdateWorkplaceFailed'));
        }

        return $workRequest;
    }

    public function changeStatus($_, array $args)
    {
        try {
            $updateInput = collect($args)->only(['status', 'response'])->toArray();

            switch($args['status']) {
                case 'PENDING':
                    WorkRequest::restore($args['requestIds']);
                    break;

                default:
                    WorkRequest::exclude($args['requestIds'], $updateInput);
                    if (array_key_exists('notify', $args) && $args['notify'])
                        $this->notifyUsers($args);
                    break;
            }
            
        } catch (\Exception $e) {
            throw new CustomException(__('lang.ChangeRequestsFailed'));
        }

        return __('lang.RequestChanged');
    }

    protected function notifyUsers(array $args)
    {
        try {
               
            foreach($args['users'] as $user) {

                $responseMsg = 'Your workplace request # ' 
                    . $user['requestId'] . ' has been ' 
                    . strtolower($args['status']);
    
                if (array_key_exists('response', $args) && $args['response']) 
                    $responseMsg .= '. '. $args['response'];

                SendPushNotification::dispatch(
                    $this->userToken($user['userId']), 
                    $responseMsg, 
                    'Qruz to Work',
                    ['view' => 'WorkRequest', 'id' => $user['requestId']]
                );
            }
        } catch (\Exception $e) {
            //
        }
    }
    
    public function destroy($_, array $args)
    {
        try {
            WorkRequest::whereIn('id', $args['id'])->delete();
        } catch (\Exception $e) {
            throw new CustomException(__('lang.DeleteRequestFailed'));
        }

        return __('lang.RequestDeleted');
    }
}
