<?php

namespace Aeva\Seats\Application\GraphQL\Mutations;

use Aeva\Seats\Domain\Repository\Eloquent\Mutations\SeatsTripPosTransactionRepository;

class SeatsTripPosTransactionResolver
{
    private $seatsTripPosTransactionRepository;

    public function __construct(SeatsTripPosTransactionRepository $seatsTripPosTransactionRepository)
    {
        $this->seatsTripPosTransactionRepository = $seatsTripPosTransactionRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        return $this->seatsTripPosTransactionRepository->create($args);
    }
}
