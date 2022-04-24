<?php

namespace App\Repository\Eloquent\Mutations;

use App\User;
use App\SchoolRequest;
use App\Jobs\SendPushNotification;
use App\Traits\HandleDeviceTokens;
use App\Exceptions\CustomException;
use App\Repository\Eloquent\BaseRepository;

class SchoolRequestRepository extends BaseRepository
{
    use HandleDeviceTokens;

    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function create(array $args)
    {
        try {
            $input = collect($args)->except(['directive'])->toArray();

            User::updateSecondaryNumber($args['contact_phone']);

            $schoolRequest = SchoolRequest::create($input);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.create_school_request_failed'));
        }

        return $schoolRequest;
    }

    public function update(array $args)
    {
        try {
            $input = collect($args)->except(['id', 'directive'])->toArray();
            $schoolRequest = SchoolRequest::findOrFail($args['id']);

            if (array_key_exists('contact_phone', $args) && $args['contact_phone'])
                User::updateSecondaryNumber($args['contact_phone']);
    
            $schoolRequest->update($input);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.update_school_request_failed'));
        }

        return $schoolRequest;
    }

    public function changeStatus(array $args)
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
            throw new CustomException(__('lang.change_request_failed'));
        }

        return __('lang.request_changed');
    }

    public function destroy(array $args)
    {
        try {
            SchoolRequest::whereIn('id', $args['id'])->delete();
        } catch (\Exception $e) {
            throw new CustomException(__('lang.delete_request_failed'));
        }

        return __('lang.request_deleted');
    
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
                    'Aeva to School',
                    ['view' => 'SchoolRequest', 'id' => $user['requestId']]
                );
            }
        } catch (\Exception $e) {
            //
        }
    }
}
