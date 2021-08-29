<?php

namespace App\Http\Controllers\Queries;

use App\Repository\Eloquent\Queries\BusinessTripAttendanceRepository; 
use Illuminate\Http\Request;

class BusinessTripAttendanceController
{

    private $businessTripAttendanceRepository;
  
    public function __construct(BusinessTripAttendanceRepository $businessTripAttendanceRepository)
    {
        $this->businessTripAttendanceRepository = $businessTripAttendanceRepository;
    }

    public function businessTripAttendance(Request $request, $trip_id)
    {
        $request = $request->all();
        $request['trip_id'] = $trip_id;

        return $this->businessTripAttendanceRepository->invoke($request);
    }
}