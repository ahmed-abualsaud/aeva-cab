<?php

namespace App\Http\Controllers\DriverApp\Queries;

use App\Repository\Eloquent\Queries\BusinessTripAttendanceRepository; 
use Illuminate\Http\Request;

class BusinessTripAttendanceController
{

    private $businessTripAttendanceRepository;
  
    public function __construct(BusinessTripAttendanceRepository $businessTripAttendanceRepository)
    {
        $this->businessTripAttendanceRepository = $businessTripAttendanceRepository;
    }

    public function businessTripAttendance(Request $req, $trip_id)
    {
        $req = $req->all();
        $req['trip_id'] = $trip_id;

        return $this->businessTripAttendanceRepository->invoke($req);
    }
}