<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\Driver;
use App\BusinessTripChat;
use App\Jobs\SendOtp;
use App\Mail\DefaultMail;
use App\Events\MessageSent;
use App\Jobs\SendPushNotification;
use App\Traits\HandleDeviceTokens;
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
        if ($args['recipientType'] == "USER") {
            $recipient = User::select('phone', 'email', 'device_id')
                ->whereIn('id', $args['recipientID'])
                ->get();
        } else {
            $recipient = Driver::select('phone', 'email', 'device_id')
                ->whereIn('id', $args['recipientID'])
                ->get();
        }

        $phones = $recipient->pluck('phone')->filter()->toArray();
        $emails = $recipient->pluck('email')->filter()->toArray();
        $tokens = $recipient->pluck('device_id')->filter()->toArray();
        
        if ($args['email'] && $emails) 
            Mail::bcc($emails)->send(new DefaultMail($args['message']));
        if ($args['sms'] && $phones) 
            SendOtp::dispatch(implode(",", $phones), $args['message']);
        if ($args['push'] && $tokens) 
            SendPushNotification::dispatch($tokens, $args['message']);

        return "Message has been sent";
    }

    public function sendBusinessTripChatMessage($_, array $args)
    {
        $input = collect($args)->except(['directive', 'driver_id', 'trip_id'])->toArray();
        $msg = BusinessTripChat::create($input);

        $guard = strtolower(str_replace('App\\', '', $args['sender_type']));
        $sender = auth($guard)->user();

        switch ($args['sender_type']) {
            case "App\\User":
                $tokens = $this->getBusinessTripUsersToken($args['trip_id'], null, $sender->id);
                array_push($tokens, $this->getDriverToken($args['driver_id']));
                break;
            case "App\\Driver":
                $tokens = $this->getBusinessTripUsersToken($args['trip_id'], null, null);
                break;
            default:
                $tokens = $this->getBusinessTripUsersToken($args['trip_id'], null, null);
                array_push($tokens, $this->getDriverToken($args['driver_id']));
                break;
        }

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

        broadcast(new MessageSent('App.BusinessTrip.'.$args['log_id'], $res))->toOthers();
        SendPushNotification::dispatch($tokens, $msg['message'], $sender->name);

        $msg->time = date("h:i a");
        
        return $msg;
    }

    public function sendChatMessage($_, array $args)
    {
        $input = collect($args)->except(['directive', 'trip_type', 'trip_id'])->toArray();
        $input['log_id'] = $args['trip_id'];
        $msg = BusinessTripChat::create($input);

        $guard = strtolower(str_replace('App\\', '', $args['sender_type']));
        $sender = auth($guard)->user();

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

        $channel = str_replace("\\", ".", $args['trip_type']) .'.'. $args['trip_id'];

        broadcast(new MessageSent($channel, $res))->toOthers();

        $msg->time = date("h:i a");
        
        return $msg;
    }
}
