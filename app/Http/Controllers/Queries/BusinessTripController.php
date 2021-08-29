<?php

namespace App\Http\Controllers\Queries;

use App\Repository\Queries\BusinessTripRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\BusinessTrip;
use App\BusinessTripStation;
use Illuminate\Validation\Rule;

class BusinessTripController 
{

    private $businessTripRepository;
  
    public function __construct(BusinessTripRepositoryInterface $businessTripRepository)
    {
        $this->businessTripRepository = $businessTripRepository;
    }

    public function show($id)
    {
        try {
            $data = BusinessTrip::findOrFail($id);
            $response = [
                'success' => true,
                'message' => 'Business Trip Details',
                'data' => $data
            ];
            return $response;
        } catch (ModelNotFoundException $e) {
            $response = [
                'success' => false,
                'message' => 'Not Found'
            ];
            return response()->json($response, 404);
        }     
    }

    public function driverBusinessTripsSchedule(Request $request, $driver_id)
    {
        $request = $request->all();
        $request['driver_id'] = $driver_id;

        $validator = Validator::make($request,[
            'day' => [
                'required', 
                Rule::in(['saturday','sunday','monday','tuesday','wednesday','thursday','friday'])
            ]
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        $data = $this->businessTripRepository->driverTrips($request);

        return [
            'success' => true,
            'message' => 'Driver Business Trips Schedule',
            'data' => $data,
        ];
    }

    public function driverLiveBusinessTrips($driver_id)
    {
        $data = $this->businessTripRepository->driverLiveTrips(['driver_id' => $driver_id]);

        return [
            'success' => true,
            'message' => 'Driver Live Business Trips',
            'data' => $data,
        ];
    }

    public function businessTripStations($trip_id)
    {
        $data = BusinessTripStation::where('trip_id', $trip_id)->get();
        $response = [
            'success' => true,
            'message' => 'Business Trip Stations',
            'data' => $data
        ];

        return $response;
    }
}