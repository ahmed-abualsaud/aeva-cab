<?php

namespace App\GraphQL\Mutations;

use App\Repository\Mutations\SeatsTripEventRepositoryInterface;

class SeatsTripEventResolver
{
    private $seatsTripEventRepository;

    public function __construct(SeatsTripEventRepositoryInterface $seatsTripEventRepository)
    {
        $this->seatsTripEventRepository = $seatsTripEventRepository;
    }

    public function changeDriverStatus($_, array $args)
    {
        return $this->seatsTripEventRepository->changeDriverStatus($args);
    }

    public function startTrip($_, array $args)
    {
        return $this->seatsTripEventRepository->startTrip($args);
    }

    public function updateDriverLocation($_, array $args)
    {
        return $this->seatsTripEventRepository->updateDriverLocation($args);
    }

    public function atStation($_, array $args)
    {
        return $this->seatsTripEventRepository->atStation($args);
    }

    public function pickUser($_, array $args)
    {
        return $this->seatsTripEventRepository->pickUser($args);
    }

    public function dropUser($_, array $args)
    {
        return $this->seatsTripEventRepository->dropUser($args);
    }

    public function endTrip($_, array $args)
    {
        return $this->seatsTripEventRepository->endTrip($args);
    }

    public function destroy($_, array $args)
    {
        return $this->seatsTripEventRepository->destroy($args);
    }
}
