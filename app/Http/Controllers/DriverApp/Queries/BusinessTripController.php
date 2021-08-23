<?php

namespace App\Http\Controllers\DriverApp\Queries;

use App\Repository\Queries\BusinessTripRepositoryInterface;
use Illuminate\Http\Request;

class BusinessTripController 
{
    private $businessTripRepository;
  
    public function __construct(BusinessTripRepositoryInterface $businessTripRepository)
    {
        $this->businessTripRepository = $businessTripRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function driverTrips(Request $req, $driver_id)
    {
        $req = $req->all();
        $req['driver_id'] = $driver_id;

        return $this->businessTripRepository->driverTrips($req);
    }

    public function driverLiveTrips($driver_id)
    {
        return $this->businessTripRepository->driverLiveTrips(['driver_id' => $driver_id]);
    }
}