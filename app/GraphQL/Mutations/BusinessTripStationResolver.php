<?php

namespace App\GraphQL\Mutations;

use App\Repository\Mutations\BusinessTripStationRepositoryInterface;

class BusinessTripStationResolver
{
    private $businessTripStationRepository;

    public function  __construct(BusinessTripStationRepositoryInterface $businessTripStationRepository)
    {
        $this->businessTripStationRepository = $businessTripStationRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function update($_, array $args)
    {
        return $this->businessTripStationRepository->update($args);
    }

    public function assignUser($_, array $args)
    {
        return $this->businessTripStationRepository->assignUser($args);
    }

    public function acceptStation($_, array $args)
    {
        return $this->businessTripStationRepository->acceptStation($args);
    }

    public function destroy($_, array $args)
    {
        return $this->businessTripStationRepository->destroy($args);
    }
}
