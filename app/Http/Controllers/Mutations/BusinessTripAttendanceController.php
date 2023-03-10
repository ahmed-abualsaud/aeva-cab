<?php

namespace App\Http\Controllers\Mutations;

use App\Repository\Eloquent\Mutations\BusinessTripAttendanceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BusinessTripAttendanceController 
{

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

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }
        try {
            $data = $this->businessTripAttendanceRepository->create($request->all());
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }

        $response = [
            'success' => true,
            'message' => 'Attendance created successfully',
            'data' => $data
        ];

        return $response;
    }
}