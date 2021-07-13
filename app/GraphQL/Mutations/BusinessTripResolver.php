<?php

namespace App\GraphQL\Mutations;

use App\Repository\Mutations\BusinessTripRepositoryInterface;

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
    public function create($_, array $args)
    {
        return $this->businessTripRepository->create($args);
    }

    public function update($_, array $args)
    {
        return $this->businessTripRepository->update($args);
    }

    public function updateRoute($_, array $args)
    {
        return $this->businessTripRepository->updateRoute($args);
    }

    public function copy($_, array $args)
    {
        return $this->businessTripRepository->copy($args);
    }

    public function inviteUser($_, array $args)
    {
        return $this->businessTripRepository->inviteUser($args);
    }

    public function createSubscription($_, array $args)
    {
        return $this->businessTripRepository->createSubscription($args);
    }

    public function confirmSubscription($_, array $args) 
    {
        return $this->businessTripRepository->confirmSubscription($args);
    }

    public function deleteSubscription($_, array $args)
    {
        return $this->businessTripRepository->deleteSubscription($args);
    }

    public function verifySubscription($_, array $args)
    {
        return $this->businessTripRepository->verifySubscription($args);
    }

    
}
