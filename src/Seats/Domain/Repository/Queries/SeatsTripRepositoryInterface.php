<?php

namespace Aeva\Seats\Domain\Repository\Queries;

Interface SeatsTripRepositoryInterface
{
    public function userLiveTrips(array $args);
    public function driverTrips(array $args);
    public function driverLiveTrips(array $args);
    public function seatsLineStationsTrips(array $args);
}