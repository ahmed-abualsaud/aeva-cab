<?php

namespace App\GraphQL\Mutations;

use App\Repository\Mutations\CommunicationRepositoryInterface;


class CommunicationResolver
{
    private $communicationRepository;

    public function  __construct(CommunicationRepositoryInterface $communicationRepository)
    {
        $this->communicationRepository = $communicationRepository;
    }

     /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function sendDirectMessage($_, array $args)
    {
        return $this->communicationRepository->sendDirectMessage($args);
    }

    public function sendBusinessTripChatMessage($_, array $args)
    {
        return $this->communicationRepository->sendBusinessTripChatMessage($args);
    }
}
