<?php

namespace Aeva\Seats\Application\GraphQL\Queries;

use Aeva\Seats\Domain\Repository\Queries\SeatsTripRepositoryInterface;

class SeatsTripResolver
{
    private $seatsTripRepository;

    public function __construct(SeatsTripRepositoryInterface $seatsTripRepository)
    {
        $this->seatsTripRepository = $seatsTripRepository;
    }
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function userLiveTrips($_, array $args)
    {
        return $this->seatsTripRepository->userLiveTrips($args);
    }

    public function driverTrips($_, array $args)
    {
        return $this->seatsTripRepository->driverTrips($args);
    }

    public function driverLiveTrips($_, array $args)
    {
        return $this->seatsTripRepository->driverLiveTrips($args);
    }

    public function seatsLineStationsTrips($_, array $args)
    {
        return $this->seatsTripRepository->seatsLineStationsTrips($args);
    }
}
