<?php

namespace App\Repository\Queries;

interface CommunicationRepositoryInterface
{
    public function businessTripChatMessages(array $args);
    public function businessTripPrivateChatUsers(array $args);
}