<?php

namespace App\Http\Controllers\DriverApp\Queries;

use App\Repository\Eloquent\Queries\BusinessTripAttendanceRepository; 

class BusinessTripAttendanceController
{

    private $businessTripAttendanceRepository;
  
    public function __construct(BusinessTripAttendanceRepository $businessTripAttendanceRepository)
    {
        $this->businessTripAttendanceRepository = $businessTripAttendanceRepository;
    }

    public function businessTripAttendance($trip_id, $date = null)
    {
        $args['trip_id'] = $trip_id;

        if($date != null)
            $args['date'] = $date;
        return $this->businessTripAttendanceRepository->invoke($args);
    }
}