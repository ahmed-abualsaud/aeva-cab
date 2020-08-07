<?php

namespace App\GraphQL\Queries;

use App\Message;

class CommunicationResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function chatMessages($_, array $args)
    {
        return Message::where('trip_id', $args['trip_id'])
            ->where('trip_type', $args['trip_type'])
            ->selectRaw('messages.*, DATE_FORMAT(created_at, "%l:%i %p") as time')
            ->get();
    }
}
