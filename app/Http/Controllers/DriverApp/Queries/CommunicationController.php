<?php

namespace App\Http\Controllers\DriverApp\Queries;

use App\Repository\Queries\CommunicationRepositoryInterface;

class CommunicationController
{
    private $communicationRepository;
  
    public function __construct(CommunicationRepositoryInterface $communicationRepository)
    {
        $this->communicationRepository =  $communicationRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function businessTripChatMessages($log_id, $user_id, $is_private)
    {
        return $this->communicationRepository->businessTripChatMessages(
            [
                'log_id'     => $log_id,
                'user_id'    => $user_id,
                'is_private' => $is_private
            ]
        );
    }

    public function businessTripPrivateChatUsers($log_id)
    {
        return $this->communicationRepository->businessTripPrivateChatUsers(['log_id' => $log_id]);
    }
}