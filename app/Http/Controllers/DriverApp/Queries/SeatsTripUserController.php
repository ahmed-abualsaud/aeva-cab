<?php

namespace App\Http\Controllers\DriverApp\Queries;

use App\Repository\Eloquent\Queries\SeatsTripUserRepository;

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
    public function users($trip_id, $trip_time, $status, $station_id = null)
    {
        $args = [
                    'trip_id'    => $trip_id,
                    'trip_time'  => $trip_time,
                    'status'     => $status
                ];
        if($station_id != null)
            $args['station_id'] = $station_id;

        return $this->seatsTripUserRepository->invoke($args);
    }
}