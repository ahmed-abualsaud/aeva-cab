<?php

namespace App\Http\Controllers\Mutations;

use App\Repository\Mutations\BusinessTripEventRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BusinessTripEventController 
{

    private $businessTripEventRepository;

    public function __construct(BusinessTripEventRepositoryInterface $businessTripEventRepository)
    {
        $this->businessTripEventRepository = $businessTripEventRepository;
    }

    public function ready(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'trip_id' => ['required'],
            'trip_time' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required']
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        try {
            $data = $this->businessTripEventRepository->ready($request->all());
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }

        $response = [
            'success' => true,
            'message' => 'Driver is ready to start the trip',
            'data' => $data
        ];

        return $response;
    }

    public function start(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'trip_id' => ['required'],
            'trip_time' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required']
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        try {
            $data = $this->businessTripEventRepository->start($request->all());
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }

        $response = [
            'success' => true,
            'message' => 'Trip started successfully',
            'data' => $data
        ];

        return $response;
    }

    public function atStation(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'eta' => ['required'],
            'station_id' => ['required'],
            'station_name' => ['required'],
            'log_id' => ['required'],
            'trip_id' => ['required'],
            'trip_name' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required']
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        try {
            $data = $this->businessTripEventRepository->atStation($request->all());
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }

        $response = [
            'success' => true,
            'message' => 'Vehicle arrived to the station successfully',
            'data' => $data
        ];

        return $response;
    }

    public function changeAttendanceStatus(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'date' => ['required'],
            'trip_id' => ['required'],
            'trip_name' => ['required'],
            'user_id' => ['required'],
            'user_name' => ['required'],
            'is_absent' => ['required'],
            'log_id' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'driver_id' => ['required'],
            'by' => ['required', Rule::in(['driver', 'user'])]
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        try {
            $data = $this->businessTripEventRepository->changeAttendanceStatus($request->all());
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }

        $response = [
            'success' => true,
            'message' => 'Attendance status changed successfully',
            'data' => $data
        ];

        return $response;
    }

    public function pickUsers(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'trip_id' => ['required'],
            'trip_name' => ['required'],
            'trip_time' => ['required'],
            'driver_id' => ['required'],
            'log_id' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'users' => ['required']
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        try {
            $data = $this->businessTripEventRepository->pickUsers($request->all());
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }

        $response = [
            'success' => true,
            'message' => 'Users picked up successfully',
            'data' => $data
        ];

        return $response;
    }

    public function dropUsers(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'trip_id' => ['required'],
            'trip_name' => ['required'],
            'trip_time' => ['required'],
            'driver_id' => ['required'],
            'log_id' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'users' => ['required']
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        try {
            $data = $this->businessTripEventRepository->dropUsers($request->all());
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }

        $response = [
            'success' => true,
            'message' => 'Users dropped off successfully',
            'data' => $data
        ];

        return $response;
    }

    public function updateDriverLocation(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'log_id' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required']
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        try {
            $data = $this->businessTripEventRepository->updateDriverLocation($request->all());
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }

        $response = [
            'success' => true,
            'message' => 'Driver location updated successfully',
            'data' => $data
        ];

        return $response;
    }

    public function end(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'trip_id' => ['required']
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        try {
            $data = $this->businessTripEventRepository->end($request->all());
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }

        $response = [
            'success' => true,
            'message' => 'Trip ended successfully',
            'data' => $data
        ];

        return $response;
    }
}