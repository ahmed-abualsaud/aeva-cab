<?php

namespace App\GraphQL\Queries;

use App\BusinessTripChat;

class CommunicationResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function buisnessTripChatMessages($_, array $args)
    {
        return BusinessTripChat::with('sender:id,name')
            ->where('log_id', $args['log_id'])
            ->selectRaw('business_trip_chat.*, DATE_FORMAT(created_at, "%l:%i %p") as time')
            ->get();
    }

    public function chatMessages($_, array $args)
    {
        return BusinessTripChat::with('sender:id,name')
            ->where('log_id', $args['trip_id'])
            ->selectRaw('business_trip_chat.*, DATE_FORMAT(created_at, "%l:%i %p") as time')
            ->get();
    }
}
