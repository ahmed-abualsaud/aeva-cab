<?php

namespace App\Repository\Queries;

interface CommunicationRepositoryInterface
{
    public function businessTripChatMessages(array $args);
    public function privateChatUsers(array $args);
    public function userPrivateChatMessages(array $args);
}