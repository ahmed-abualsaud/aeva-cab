<?php

namespace Qruz\Seats\Application\GraphQL\Queries;

use Qruz\Seats\Domain\Repository\Queries\SeatsTripBookingRepositoryInterface;

class SeatsTripBookingResolver
{
    private $seatsTripBookingRepository;

    public function __construct(SeatsTripBookingRepositoryInterface $seatsTripBookingRepository)
    {
        $this->seatsTripBookingRepository = $seatsTripBookingRepository;
    }
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function pre($_, array $args)
    {
        return $this->seatsTripBookingRepository->pre($args);
    }
}
