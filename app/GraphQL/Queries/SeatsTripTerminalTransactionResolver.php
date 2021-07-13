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
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function stats($_, array $args)
    {
        return $this->seatsTripTerminalTransaction->stats($args);
    }
}
