<?php

namespace App\Http\Controllers\DriverApp\Mutations;

use App\Repository\Mutations\SeatsTripEventRepositoryInterface;
use Illuminate\Http\Request;

class SeatsTripEventController 
{
    private $seatsTripEventRepository;

    public function __construct(SeatsTripEventRepositoryInterface $seatsTripEventRepository)
    {
        $this->seatsTripEventRepository = $seatsTripEventRepository;
    }

    public function ready(Request $args)
    {
        return $this->seatsTripEventRepository->ready($args->all());
    }

    public function start(Request $args)
    {
        return $this->seatsTripEventRepository->start($args->all());
    }

    public function updateDriverLocation(Request $args)
    {
        return $this->seatsTripEventRepository->updateDriverLocation($args->all());
    }

    public function atStation(Request $args)
    {
        return $this->seatsTripEventRepository->atStation($args->all());
    }

    public function pickUser(Request $args)
    {
        return $this->seatsTripEventRepository->pickUser($args->all());
    }

    public function dropUser(Request $args)
    {
        return $this->seatsTripEventRepository->dropUser($args->all());
    }

    public function end(Request $args)
    {
        return $this->seatsTripEventRepository->end($args->all());
    }
}