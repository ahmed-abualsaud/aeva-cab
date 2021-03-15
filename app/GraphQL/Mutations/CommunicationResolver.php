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
            $recipient = $args['recipient_type']::select('phone', 'email', 'device_id')
                ->whereIn('id', $args['recipient_id'])
                ->get();
    
            $phones = $recipient->pluck('phone')->filter()->toArray();
            $emails = $recipient->pluck('email')->filter()->toArray();
            $tokens = $recipient->pluck('device_id')->filter()->toArray();
            
            if ($args['email'] && $emails) 
                Mail::bcc($emails)->send(new DefaultMail($args['message'], $args['title']));
            if ($args['sms'] && $phones) 
                SendOtp::dispatch(implode(",", $phones), $args['message']);
            if ($args['push'] && $tokens) 
                SendPushNotification::dispatch($tokens, $args['message'], $args['title']);
    
            return "Message has been sent";
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
        } else {
            $this->notifyGroup($args, $message['message'], $sender);
            $this->broadcastMessage($message, $sender, $args['log_id']);
        }

        return $message;
    }

    protected function createMessage($args)
    {
        try {
            $input = Arr::except($args, ['directive', 'driver_id', 'trip_id']);
            if(array_key_exists('recipient_id', $args) && $args['recipient_id']) 
                $input['is_direct'] = true;
            $msg = BusinessTripChat::create($input);
            $msg->time = date("h:i a");
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

    protected function notifyRecipient(array $args, $msg, $sender)
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
                $msg, 
                $sender->name,
                ['view' => 'BusinessTripDirectMessage', 'id' => $args['trip_id']]
            );
        } catch(\Exception $e) {
            //
        }
    }

    protected function notifyGroup(array $args, $msg, $sender)
    {
        try {
            switch ($args['sender_type']) {
                case "App\\User":
                    $tokens = $this->tripUsersTokenWithout($args['trip_id'], $sender->id);
                    array_push($tokens, $this->driverToken($args['driver_id']));
                    break;
                case "App\\Driver":
                    $tokens = $this->tripUsersToken($args['trip_id']);
                    break;
                default:
                    $tokens = $this->tripUsersToken($args['trip_id']);
                    array_push($tokens, $this->driverToken($args['driver_id']));
                    break;
            }
    
            SendPushNotification::dispatch(
                $tokens, 
                $msg, 
                $sender->name,
                ['view' => 'BusinessTripGroupChat', 'id' => $args['trip_id']]
            );
        } catch (\Exception $e) {
            //
        }
    }

    protected function broadcastMessage($msg, $sender, $log_id)
    {
        try {
            $res = [ 
                "id" => $msg['id'],
                "message" => $msg['message'],
                "created_at" => date("Y-m-d H:i:s"),
                "time" => date("h:i a"),
                "sender" => [
                    "id" => $sender->id,
                    "name" => $sender->name,
                    "__typename" => "Sender"
                ],
                "sender_type" => $msg['sender_type'],
                "__typename" => "Message"
            ];
    
            broadcast(new MessageSent('App.BusinessTrip.'.$log_id, $res))->toOthers();
        } catch (\Exception $e) {
            //
        }
    }
}
