<?php

namespace App\Repository\Mutations;

interface BusinessTripRepositoryInterface
{
    public function updateRoute(array $args);
    public function copy(array $args);
    public function inviteUser(array $args);
    public function createSubscription(array $args);
    public function confirmSubscription(array $args);
    public function deleteSubscription(array $args);
    public function verifySubscription(array $args);
}