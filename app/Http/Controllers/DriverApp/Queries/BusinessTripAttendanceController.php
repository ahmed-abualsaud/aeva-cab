<?php

namespace App\Http\Controllers\DriverApp\Queries;

use App\Repository\Eloquent\Queries\BusinessTripAttendanceRepository; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\HandleValidatorMessages;

class BusinessTripAttendanceController
{
    use HandleValidatorMessages;

    private $businessTripAttendanceRepository;
  
    public function __construct(BusinessTripAttendanceRepository $businessTripAttendanceRepository)
    {
        $this->businessTripAttendanceRepository = $businessTripAttendanceRepository;
    }

    public function businessTripAttendance(Request $request, $trip_id)
    {
        $request = $request->all();
        $request['trip_id'] = $trip_id;

        $validator = Validator::make($request,[
            'trip_id' => ['required', 'exists:business_trip_attendance,trip_id'],
            'date' => ['exists:business_trip_attendance,date']
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);

        return $this->businessTripAttendanceRepository->invoke($request);
    }
}