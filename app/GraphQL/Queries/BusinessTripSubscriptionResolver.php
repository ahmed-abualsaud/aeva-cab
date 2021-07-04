<?php

namespace App\GraphQL\Queries;

use App\Repository\Queries\BusinessTripSubscriptionRepositoryInterface;

class BusinessTripSubscriptionResolver
{

    private $businessTripSubscriptionRepository;
  
    public function __construct(BusinessTripSubscriptionRepositoryInterface $businessTripSubscriptionRepository)
    {
        $this->businessTripSubscriptionRepository = $businessTripSubscriptionRepository;
    }

    public function businessTripSubscribedUsers($_, array $args)
    {
        return $this->businessTripSubscriptionRepository->businessTripSubscribedUsers($args);
    }

    public function businessTripStationUsers($_, array $args)
    {
        return $this->businessTripSubscriptionRepository->businessTripStationUsers($args);
    }

    public function businessTripSubscribers($_, array $args)
    {
        return $this->businessTripSubscriptionRepository->businessTripSubscribers($args);
    }

    public function businessTripUsersStatus($_, array $args)
    {
        return $this->businessTripSubscriptionRepository->businessTripUsersStatus($args);
    }

    public function businessTripUserStatus($_, array $args)
    {
        return $this->businessTripSubscriptionRepository->businessTripUserStatus($args);
    }

}
