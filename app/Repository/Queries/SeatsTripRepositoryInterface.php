<?php

namespace App\Repository\Queries;

Interface SeatsTripRepositoryInterface
{
    public function userLiveTrips(array $args);
    public function driverTrips(array $args);
    public function driverLiveTrips(array $args);
}