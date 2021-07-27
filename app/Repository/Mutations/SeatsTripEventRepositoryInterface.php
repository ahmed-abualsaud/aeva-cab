<?php

namespace App\Repository\Mutations;

interface SeatsTripEventRepositoryInterface
{
    public function changeDriverStatus(array $args);
    public function startTrip(array $args);
    public function updateDriverLocation(array $args);
    public function atStation(array $args);
    public function pickUser(array $args);
    public function dropUser(array $args);
    public function endTrip(array $args);
}