<?php

namespace Aeva\Seats\Domain\Repository\Queries;

interface SeatsTripTerminalTransactionRepositoryInterface
{
    public function vehiclesStats(array $args);
    public function timeStats(array $args);
}