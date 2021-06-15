<?php

namespace App\GraphQL\Mutations;

use App\Jobs\SendOtp;
use App\BusinessTripChat;
use App\Mail\DefaultMail;
use App\Events\MessageSent;
use Illuminate\Support\Arr;
use App\Jobs\SendPushNotification;
use App\Traits\HandleDeviceTokens;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Mail;

class CommunicationResolver
{
    use HandleDeviceTokens;

     /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function sendDirectMessage($_, array $args)
    {
        try {
            if ($args['email']) {
                $emails = $args['recipient_type']::select('email');

                if (array_key_exists('all', $args) && !$args['all'])
                    $emails = $emails->whereIn('id', $args['recipient_id']);

                $emails = $emails->pluck('email')->filter()->flatten()->toArray();

                if ($emails)
                    Mail::bcc($emails)->send(new DefaultMail($args['message'], $args['title']));

            }

            if ($args['sms']) {
                $phones = $args['recipient_type']::select('phone');

                if (array_key_exists('all', $args) && !$args['all'])
                    $phones = $phones->whereIn('id', $args['recipient_id']);

                $phones = $phones->pluck('phone')->filter()->flatten()->toArray();

                if ($phones)
                    SendOtp::dispatch(implode(',', $phones), $args['message']);
            }

            if ($args['push']) {
                $tokens = $args['recipient_type']::select('device_id');

                if (array_key_exists('all', $args) && !$args['all'])
                    $tokens = $tokens->whereIn('id', $args['recipient_id']);

                $tokens = $tokens->pluck('device_id')->filter()->flatten()->toArray();

                if ($tokens)
                    if (count($tokens) > 1000) {
                        $chunks = array_chunk($tokens, 1000);
                        foreach($chunks as $chunk) {
                            SendPushNotification::dispatch($chunk, $args['message'], $args['title']);
                        }
                    }
                    SendPushNotification::dispatch($tokens, $args['message'], $args['title']);
            }
    
            return 'Message has been sent';
        } catch(\Exception $e) {
            throw new CustomException('We could not able to send message to selected recipients!');
        }
    }

    public function sendBusinessTripChatMessage($_, array $args)
    {
        $message = $this->createMessage($args);
        $sender = $this->getSender($args['sender_type']);

        if(array_key_exists('recipient_id', $args) && $args['recipient_id']) {
            $this->notifyRecipient($args, $message['message'], $sender);
            $args['private'] = true;
        } else {
            $this->notifyGroup($args, $message['message'], $sender);
            $args['private'] = false;
        }

        $this->broadcastMessage($message, $sender, $args);

        return $message;
    }

    protected function createMessage($args)
    {
        try {
            $input = Arr::except($args, ['directive', 'driver_id', 'trip_id', 'trip_name']);
            if(array_key_exists('recipient_id', $args) && $args['recipient_id']) 
                $input['is_private'] = true;
            $msg = BusinessTripChat::create($input);
            $msg->time = date('h:i a');
            return $msg;
        } catch (\Exception $e) {
            throw new CustomException('We could not able to save this message!');
        }
    }

    protected function getSender($sender_type)
    {
        try {
            $guard = strtolower(str_replace('App\\', '', $sender_type));
            return auth($guard)->user();
        } catch (\Exception $e) {
            //
        }
    }

    protected function notifyRecipient($args, $msg, $sender)
    {
        try {
            switch($args['sender_type']) {
                case 'App\\User':
                    $token = $this->driverToken($args['recipient_id']);
                break;
                default:
                    $token = $this->userToken($args['recipient_id']);
            }

            SendPushNotification::dispatch(
                $token, 
                $sender->name.': '.$msg, 
                $args['trip_name'],
                [
                    'view' => 'BusinessTripDirectMessage', 
                    'id' => $args['trip_id'], 
                    'sender_id' => $args['sender_id']
                ]
            );
        } catch(\Exception $e) {
            //
        }
    }

    protected function notifyGroup($args, $msg, $sender)
    {
        try {
            switch ($args['sender_type']) {
                case 'App\\User':
                    $tokens = $this->tripUsersTokenWithout($args['trip_id'], $sender->id);
                    array_push($tokens, $this->driverToken($args['driver_id']));
                    break;
                case 'App\\Driver':
                    $tokens = $this->tripUsersToken($args['trip_id']);
                    break;
                default:
                    $tokens = $this->tripUsersToken($args['trip_id']);
                    array_push($tokens, $this->driverToken($args['driver_id']));
                    break;
            }
    
            SendPushNotification::dispatch(
                $tokens, 
                $sender->name.': '.$msg, 
                $args['trip_name'],
                [
                    'view' => 'BusinessTripGroupChat', 
                    'id' => $args['trip_id']
                ]
            );
        } catch (\Exception $e) {
            //
        }
    }

    protected function broadcastMessage($msg, $sender, $args)
    {
        try {
            $res = [ 
                'id' => $msg['id'],
                'message' => $msg['message'],
                'created_at' => date('Y-m-d H:i:s'),
                'time' => date('h:i a'),
                'sender' => [
                    'id' => $sender->id,
                    'name' => $sender->name,
                    '__typename' => 'Sender'
                ],
                'sender_type' => $msg['sender_type'],
                '__typename' => 'Message'
            ];
    
            broadcast(new MessageSent($this->getChannelName($args), $res))->toOthers();
        } catch (\Exception $e) {
            //
        }
    }

    protected function getChannelName($args)
    {
        if ($args['private']) {
            $user_id = $args['sender_type'] == 'App\\User' 
                ? $args['sender_id'] 
                : $args['recipient_id'];

            return 'App.BusinessTripPrivateChat.'.$args['log_id'].'.'.$user_id;
        }

        return 'App.BusinessTrip.'.$args['log_id'];
    }
}
