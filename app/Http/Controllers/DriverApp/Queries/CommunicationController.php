<?php

namespace App\Http\Controllers\DriverApp\Queries;

use App\Repository\Queries\CommunicationRepositoryInterface;
use Illuminate\Http\Request;

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
    public function businessTripChatMessages(Request $req, $user_id)
    {
        $req = $req->all();
        $req['user_id'] = $user_id;

        return $this->communicationRepository->businessTripChatMessages($req);
    }

    public function businessTripPrivateChatUsers(Request $req)
    {
        return $this->communicationRepository->businessTripPrivateChatUsers($req->all());
    }
}