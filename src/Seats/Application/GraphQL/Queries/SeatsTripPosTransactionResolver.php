<?php

namespace Aeva\Seats\Application\GraphQL\Queries;

use Aeva\Seats\Domain\Repository\Queries\SeatsTripPosTransactionRepositoryInterface;

class SeatsTripPosTransactionResolver
{
    private $seatsTripPosTransaction;

    public function __construct(SeatsTripPosTransactionRepositoryInterface $seatsTripPosTransaction)
    {
        $this->seatsTripPosTransaction = $seatsTripPosTransaction;
    }

    public function vehiclesStats($_, array $args)
    {
        return $this->seatsTripPosTransaction->vehiclesStats($args);
    }

    public function timeStats($_, array $args)
    {
        return $this->seatsTripPosTransaction->timeStats($args);
    }
}
