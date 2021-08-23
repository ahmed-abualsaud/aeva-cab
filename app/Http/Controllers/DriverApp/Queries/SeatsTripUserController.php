<?php

namespace App\Http\Controllers\DriverApp\Queries;

use App\Repository\Eloquent\Queries\SeatsTripUserRepository;
use Illuminate\Http\Request;

class SeatsTripUserController
{
    private $seatsTripUserRepository;

    public function __construct(SeatsTripUserRepository $seatsTripUserRepository)
    {
        $this->seatsTripUserRepository = $seatsTripUserRepository;
    }
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function users(Request $req, $trip_id)
    {
        $req = $req->all();
        $req['trip_id'] = $trip_id;

        return $this->seatsTripUserRepository->invoke($req);
    }
}