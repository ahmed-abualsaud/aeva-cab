<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\Driver;
use App\Message;
use App\DeviceToken;
use App\Jobs\SendOtp;
use App\Mail\DefaultMail;
use App\Events\MessageSent;
use App\Jobs\SendPushNotification;
use Illuminate\Support\Facades\Mail;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CommunicationResolver
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function sendDirectMessage($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if ($args['recipientType'] == "USER") {
            $recipient = User::select('users.phone', 'users.email', 'device_tokens.device_id')
                ->whereIn('users.id', $args['recipientID'])
                ->leftJoin('device_tokens', function ($join) {
                    $join->on('users.id', '=', 'device_tokens.tokenable_id')
                        ->where('device_tokens.tokenable_type', '=', 'App\User');
                })
                ->get();
        } else {
            $recipient = Driver::select('drivers.phone', 'drivers.email', 'device_tokens.device_id')
                ->whereIn('drivers.id', $args['recipientID'])
                ->leftJoin('device_tokens', function ($join) {
                    $join->on('drivers.id', '=', 'device_tokens.tokenable_id')
                        ->where('device_tokens.tokenable_type', '=', 'App\Driver');
                })
                ->get();
        }

        $phones = $recipient->pluck('phone')->filter()->toArray();
        $emails = $recipient->pluck('email')->filter()->toArray();
        $tokens = $recipient->pluck('device_id')->filter()->toArray();
        
        if ($args['email']) Mail::bcc($emails)->send(new DefaultMail($args['message']));
        if ($args['sms']) SendOtp::dispatch(implode(",", $phones), $args['message']);
        if ($args['push']) SendPushNotification::dispatch($tokens, $args['message']);

        return "Message has sent";
    }

    public function sendChatMessage($_, array $args)
    {
        $input = collect($args)->except(['directive'])->toArray();
        $msg = Message::create($input);

        $sender = $args['sender_type']::select('id','name')->find($args['sender_id']);

        $res = [ 
            "id" => $msg['id'],
            "message" => $msg['message'],
            "created_at" => date("Y-m-d H:i:s"),
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

        return $msg;
    }
}
