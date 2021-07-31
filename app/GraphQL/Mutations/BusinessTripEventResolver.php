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

    public function ready($_, array $args)
    {
        return $this->businessTripEventRepository->ready($args);
    }

    public function start($_, array $args)
    {
        return $this->businessTripEventRepository->start($args);
    }

    public function atStation($_, array $args)
    {
        return $this->businessTripEventRepository->atStation($args);
    }

    public function changePickupStatus($_, array $args)
    {
        return $this->businessTripEventRepository->changePickupStatus($args);
    }

    public function changeAttendanceStatus($_, array $args)
    {
        return $this->businessTripEventRepository->changeAttendanceStatus($args);
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

    public function end($_, array $args)
    {
        return $this->businessTripEventRepository->end($args);
    }

    public function destroy($_, array $args)
    {
        return $this->businessTripEventRepository->destroy($args);
    }
}
