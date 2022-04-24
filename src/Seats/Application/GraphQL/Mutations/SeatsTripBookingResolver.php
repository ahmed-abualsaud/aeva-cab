<?php

namespace Aeva\Seats\Application\GraphQL\Mutations;

use Aeva\Seats\Domain\Repository\Eloquent\Mutations\SeatsTripBookingRepository;

class SeatsTripBookingResolver
{
    private $seatsTripBookingRepository;

    public function __construct(SeatsTripBookingRepository $seatsTripBookingRepository)
    {
        $this->seatsTripBookingRepository = $seatsTripBookingRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        return $this->seatsTripBookingRepository->create($args);
    }

    public function update($_, array $args)
    {
        return $this->seatsTripBookingRepository->update($args);

    }

    public function destroy($_, array $args)
    {
        return $this->seatsTripBookingRepository->destroy($args);
    }

}