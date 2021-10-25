<?php

namespace App\GraphQL\Queries;

use App\Repository\Queries\SeatsTripTerminalTransactionRepositoryInterface;

class SeatsTripTerminalTransactionResolver
{
    private $seatsTripTerminalTransaction;

    public function __construct(SeatsTripTerminalTransactionRepositoryInterface $seatsTripTerminalTransaction)
    {
        $this->seatsTripTerminalTransaction = $seatsTripTerminalTransaction;
    }

    public function vehiclesStats($_, array $args)
    {
        return $this->seatsTripTerminalTransaction->vehiclesStats($args);
    }

    public function timeStats($_, array $args)
    {
        return $this->seatsTripTerminalTransaction->timeStats($args);
    }
}
