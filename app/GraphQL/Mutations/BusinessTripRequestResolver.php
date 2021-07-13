<?php

namespace App\GraphQL\Mutations;

use App\Repository\Mutations\BusinessTripRequestRepositoryInterface;

class BusinessTripRequestResolver
{
    private $businessTripRequestRepository;

    public function __construct(BusinessTripRequestRepositoryInterface $businessTripRequestRepository)
    {
        $this->businessTripRequestRepository = $businessTripRequestRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function createTrip($_, array $args)
    {
        return $this->businessTripRequestRepository->createTrip($args);
    }

    public function addToTrip($_, array $args)
    {
        return $this->businessTripRequestRepository->addToTrip($args);
    }

}
