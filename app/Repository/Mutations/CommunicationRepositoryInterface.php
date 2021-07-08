<?php

namespace App\Repository\Mutations;

interface CommunicationRepositoryInterface
{
    public function sendDirectMessage(array $args);
    public function sendBusinessTripChatMessage(array $args);
}