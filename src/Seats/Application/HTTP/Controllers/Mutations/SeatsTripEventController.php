<?php

namespace Qruz\Seats\Application\Http\Controllers\Mutations;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Qruz\Seats\Domain\Repository\Mutations\SeatsTripEventRepositoryInterface;

class SeatsTripEventController 
{

    private $seatsTripEventRepository;

    public function __construct(SeatsTripEventRepositoryInterface $seatsTripEventRepository)
    {
        $this->seatsTripEventRepository = $seatsTripEventRepository;
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
            $data = $this->seatsTripEventRepository->ready($request->all());
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
            $data = $this->seatsTripEventRepository->start($request->all());
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
            $data = $this->seatsTripEventRepository->updateDriverLocation($request->all());
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

    public function atStation(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'eta' => ['required'],
            'station_id' => ['required'],
            'station_name' => ['required'],
            'log_id' => ['required'],
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
            $data = $this->seatsTripEventRepository->atStation($request->all());
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

    public function pickUser(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'booking_id' => ['required']
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        try {
            $data = $this->seatsTripEventRepository->pickUser($request->all());
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

    public function dropUser(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'log_id' => ['required'],
            'booking_id' => ['required'],
            'user_id' => ['required'],
            'trip_id' => ['required'],
            'trip_time' => ['required'],
            'driver_id' => ['required'],
            'payable' => ['required'],
            'paid' => ['required']
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        try {
            $data = $this->seatsTripEventRepository->dropUser($request->all());
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
            $data = $this->seatsTripEventRepository->end($request->all());
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