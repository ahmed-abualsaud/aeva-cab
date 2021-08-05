<?php

namespace App\GraphQL\Queries;

use App\Repository\Queries\CommunicationRepositoryInterface;

class CommunicationResolver
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
    public function businessTripChatMessages($_, array $args)
    {
        return $this->communicationRepository->businessTripChatMessages($args);
    }

    public function privateChatUsers($_, array $args)
    {
        return $this->communicationRepository->privateChatUsers($args);
    }

    public function userPrivateChatMessages($_, array $args)
    {
        return $this->communicationRepository->userPrivateChatMessages($args);
    }
}
