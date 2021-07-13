<?php

namespace App\GraphQL\Queries;

use App\Repository\Queries\SeatsTripAppTransactionRepositoryInterface;

class SeatsTripAppTransactionResolver
{
    private $seatsTripAppTransactionRepository;

    public function __construct(SeatsTripAppTransactionRepositoryInterface $seatsTripAppTransactionRepository)
    {
        $this->seatsTripAppTransactionRepository = $seatsTripAppTransactionRepository;
    }
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function stats($_, array $args)
    {
        return $this->seatsTripAppTransactionRepository->stats($args);
    }
}
