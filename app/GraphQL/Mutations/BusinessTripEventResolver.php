<?php

namespace App\GraphQL\Mutations;

use App\Repository\Mutations\BusinessTripEventRepositoryInterface;

class BusinessTripEventResolver
{
    private $businessTripEventRepository;

    public function __construct(BusinessTripEventRepositoryInterface $businessTripEventRepository)
    {
        $this->businessTripEventRepository = $businessTripEventRepository;
    }

    public function startTrip($_, array $args)
    {
        return $this->businessTripEventRepository->startTrip($args);
    }

    public function atStation($_, array $args)
    {
        return $this->businessTripEventRepository->atStation($args);
    }

    public function changeBusinessTripPickupStatus($_, array $args)
    {
        return $this->businessTripEventRepository->changeBusinessTripPickupStatus($args);
    }

    public function changeBusinessTripAttendanceStatus($_, array $args)
    {
        return $this->businessTripEventRepository->changeBusinessTripAttendanceStatus($args);
    }

    public function pickUsers($_, array $args)
    {
        return $this->businessTripEventRepository->pickUsers($args);
    }

    public function dropUsers($_, array $args)
    {
        return $this->businessTripEventRepository->dropUsers($args);
    }

    public function updateDriverLocation($_, array $args)
    {
        return $this->businessTripEventRepository->updateDriverLocation($args);
    }

    public function endTrip($_, array $args)
    {
        return $this->businessTripEventRepository->endTrip($args);
    }

    public function destroy($_, array $args)
    {
        return $this->businessTripEventRepository->destroy($args);
    }
}
