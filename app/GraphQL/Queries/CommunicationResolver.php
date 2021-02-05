<?php

namespace App\GraphQL\Queries;

use App\BusinessTripChat;
use App\Exceptions\CustomException;

class CommunicationResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function businessTripChatMessages($_, array $args)
    {
        try {
            $messages = BusinessTripChat::with('sender:id,name')
                ->where('log_id', $args['log_id'])
                ->selectRaw('business_trip_chat.*, DATE_FORMAT(created_at, "%l:%i %p") as time');
    
            if (array_key_exists('is_direct', $args) && $args['is_direct']) {
                $messages = $messages->where('is_direct', true)
                    ->where('sender_id', $args['user_id'])
                    ->orWhere('recipient_id', $args['user_id']);
            } else {
                $messages = $messages->where('is_direct', false);
            }
            
            return $messages->get();
        } catch (\Exception $e) {
            throw new CustomException('We could not able to this chat messages!');
        }

            
    }
}
