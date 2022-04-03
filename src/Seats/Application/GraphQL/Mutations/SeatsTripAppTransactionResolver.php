<?php

namespace Qruz\Seats\Application\GraphQL\Mutations;

use Qruz\Seats\Domain\Repository\Eloquent\Mutations\SeatsTripAppTransactionRepository;

class SeatsTripAppTransactionResolver
{
    private $seatsTripAppTransactionRepository;

    public function __construct(SeatsTripAppTransactionRepository $seatsTripAppTransactionRepository)
    {
        $this->seatsTripAppTransactionRepository = $seatsTripAppTransactionRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        //$args['trip_time'] = '2021-7-8 10:10:10';
        return $this->seatsTripAppTransactionRepository->create($args);
    }

    public function destroy($_, array $args)
    {
        return $this->seatsTripAppTransactionRepository->destroy($args);
    }
}
