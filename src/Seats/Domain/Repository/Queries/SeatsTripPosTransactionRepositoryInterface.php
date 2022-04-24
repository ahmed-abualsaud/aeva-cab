<?php

namespace Aeva\Seats\Domain\Repository\Queries;

interface SeatsTripPosTransactionRepositoryInterface
{
    public function vehiclesStats(array $args);
    public function timeStats(array $args);
    public function driverReport(array $args);
}