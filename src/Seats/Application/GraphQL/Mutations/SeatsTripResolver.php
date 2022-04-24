<?php

namespace Aeva\Seats\Application\GraphQL\Mutations;

use Aeva\Seats\Domain\Repository\Eloquent\Mutations\SeatsTripRepository;

class SeatsTripResolver
{
    private $seatsTripRepository;

    public function __construct(SeatsTripRepository $seatsTripRepository)
    {
        $this->seatsTripRepository = $seatsTripRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        return $this->seatsTripRepository->create($args);
    }

    public function update($_, array $args)
    {
        return $this->seatsTripRepository->update($args);
    }

    public function copy($_, array $args)
    {
        return $this->seatsTripRepository->copy($args);
    }
}
