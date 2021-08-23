<?php

namespace App\Http\Controllers\DriverApp\Queries;

use App\Repository\Queries\BusinessTripRepositoryInterface;

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
    public function driverTrips($driver_id, $day)
    {
        return $this->businessTripRepository->driverTrips(
            [
                'driver_id' => $driver_id,
                'day' => $day
            ]
        );
    }

    public function driverLiveTrips($driver_id)
    {
        return $this->businessTripRepository->driverLiveTrips(['driver_id' => $driver_id]);
    }
}