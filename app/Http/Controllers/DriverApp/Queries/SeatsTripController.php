<?php

namespace App\Http\Controllers\DriverApp\Queries;

use App\Repository\Queries\SeatsTripRepositoryInterface;

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
    public function driverTrips($driver_id, $day)
    {
        return $this->seatsTripRepository->driverTrips(
            [
                'driver_id' => $driver_id,
                'day' => $day
            ]
        );
    }

    public function driverLiveTrips($driver_id)
    {
        return $this->seatsTripRepository->driverLiveTrips(['driver_id' => $driver_id]);
    }
}