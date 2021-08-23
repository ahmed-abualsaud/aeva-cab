<?php

namespace App\Http\Controllers\DriverApp\Mutations;

use App\Repository\Mutations\CommunicationRepositoryInterface;
use Illuminate\Http\Request;

class CommunicationController 
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
    public function sendBusinessTripChatMessage(Request $args)
    {
        return $this->communicationRepository->sendBusinessTripChatMessage($args->all());
    }
}