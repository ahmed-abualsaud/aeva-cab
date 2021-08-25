<?php

namespace App\Http\Controllers\DriverApp\Mutations;

use App\Repository\Eloquent\Mutations\BusinessTripAttendanceRepository;
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

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'trip_id' => ['required'],
            'user_id' => ['required'],
            'date' => ['required']
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);
            
        return $this->businessTripAttendanceRepository->create($request->all());
    }
}