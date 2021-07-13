<?php

namespace App\Repository\Eloquent\Mutations;

use App\User;
use App\WorkRequest;
use App\Jobs\SendPushNotification;
use App\Traits\HandleDeviceTokens;
use App\Exceptions\CustomException;
use App\Repository\Eloquent\BaseRepository;

class WorkRequestRepository extends BaseRepository
{
    use HandleDeviceTokens;

    public function __construct(WorkRequest $model)
    {
        parent::__construct($model);
    }

    public function create(array $args)
    {
        try {
            $input = collect($args)->except(['directive'])->toArray();

            if (array_key_exists('contact_phone', $args) && $args['contact_phone'])
                User::updateSecondaryNumber($args['contact_phone']);

            $workRequest = $this->model->create($input);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.create_workplace_failed'));
        }

        return $workRequest;
    }

    public function update(array $args)
    {
        try {
            $input = collect($args)->except(['id', 'directive'])->toArray();
            $workRequest = $this->model->findOrFail($args['id']);

            if (array_key_exists('contact_phone', $args) && $args['contact_phone'])
                User::updateSecondaryNumber($args['contact_phone']);
    
            $workRequest->update($input);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.update_workplace_failed'));
        }

        return $workRequest;
    }

    public function changeStatus(array $args)
    {
        try {
            $updateInput = collect($args)->only(['status', 'response'])->toArray();

            switch($args['status']) {
                case 'PENDING':
                    $this->model->restore($args['requestIds']);
                    break;

                default:
                    $this->model->exclude($args['requestIds'], $updateInput);
                    if (array_key_exists('notify', $args) && $args['notify'])
                        $this->notifyUsers($args);
                    break;
            }
            
        } catch (\Exception $e) {
            throw new CustomException(__('lang.change_requests_failed'));
        }

        return __('lang.request_changed');
    }

    public function destroy(array $args)
    {
        try {
            $this->model->whereIn('id', $args['id'])->delete();
        } catch (\Exception $e) {
            throw new CustomException(__('lang.delete_request_failed'));
        }

        return __('lang.request_deleted');
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
    
}
