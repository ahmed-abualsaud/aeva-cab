<?php

namespace App\GraphQL\Mutations;

use App\SeatsTrip;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use App\Repository\Eloquent\Mutations\SeatsTripRepository;

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
