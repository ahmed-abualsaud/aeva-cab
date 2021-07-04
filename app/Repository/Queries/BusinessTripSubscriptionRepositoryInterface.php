<?php

namespace App\Repository\Queries;

use Illuminate\Support\Collection;

interface BusinessTripSubscriptionRepositoryInterface
{
    public function businessTripSubscribedUsers(array $args): Collection;
    public function businessTripStationUsers(array $args): Collection;
    public function businessTripSubscribers(array $args): Collection;
    public function businessTripUsersStatus(array $args): Collection;
    public function businessTripUserStatus(array $args);
}