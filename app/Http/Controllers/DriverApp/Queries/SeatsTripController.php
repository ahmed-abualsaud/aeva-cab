<?php

namespace App\Http\Controllers\DriverApp\Queries;

use App\Repository\Queries\SeatsTripRepositoryInterface;
use Illuminate\Http\Request;

class SeatsTripController 
{
    private $seatsTripRepository;
  
    public function __construct(SeatsTripRepositoryInterface $seatsTripRepository)
    {
        $this->seatsTripRepository = $seatsTripRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function driverTrips(Request $req, $driver_id)
    {
        $req = $req->all();
        $req['driver_id'] = $driver_id;

        return $this->seatsTripRepository->driverTrips($req);
    }

    public function driverLiveTrips($driver_id)
    {
        return $this->seatsTripRepository->driverLiveTrips(['driver_id' => $driver_id]);
    }
}