<?php

namespace Aeva\Seats\Application\GraphQL\Mutations;

use Aeva\Seats\Domain\Repository\Eloquent\Mutations\SeatsTripRatingRepository;

class SeatsTripRatingResolver
{
    private $seatsTripRatingRepository;

    public function __construct(SeatsTripRatingRepository $seatsTripRatingRepository)
    {
        $this->seatsTripRatingRepository = $seatsTripRatingRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function update($_, array $args)
    {
        return $this->seatsTripRatingRepository->update($args);
    }
}
