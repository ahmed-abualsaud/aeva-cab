<?php

namespace App\Repository\Queries;

interface SeatsTripPosTransactionRepositoryInterface
{
    public function vehiclesStats(array $args);
    public function timeStats(array $args);
    public function driverMaxSerial(array $args);
}