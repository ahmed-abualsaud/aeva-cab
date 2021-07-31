<?php

namespace App\GraphQL\Queries;

use App\Repository\Queries\BusinessTripRepositoryInterface;

class BusinessTripResolver
{
    private $businessTripRepository;
  
    public function __construct(BusinessTripRepositoryInterface $businessTripRepository)
    {
        $this->businessTripRepository = $businessTripRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function userSubscriptions($_, array $args)
    {
        return $this->businessTripRepository->userSubscriptions($args);
    }

    public function userTrips($_, array $args)
    {
        return $this->businessTripRepository->userTrips($args);
    }

    public function userLiveTrips($_, array $args)
    {
        return $this->businessTripRepository->userLiveTrips($args);
    }

    public function driverTrips($_, array $args)
    {
        return $this->businessTripRepository->driverTrips($args);
    }

    public function driverLiveTrips($_, array $args)
    {
        return $this->businessTripRepository->driverLiveTrips($args);
    }

    public function userHistory($_, array $args)
    {
        return $this->businessTripRepository->userHistory($args);
    }
}