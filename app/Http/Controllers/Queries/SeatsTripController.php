<?php

namespace App\Http\Controllers\Queries;

use App\Repository\Queries\SeatsTripRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\SeatsTrip;
use App\SeatsTripAppTransaction;
use Illuminate\Validation\Rule;

class SeatsTripController 
{

    private $seatsTripRepository;
  
    public function __construct(SeatsTripRepositoryInterface $seatsTripRepository)
    {
        $this->seatsTripRepository = $seatsTripRepository;
    }

    public function driverSeatsTrips($driver_id)
    {
        $data = SeatsTrip::where('driver_id', $driver_id)->get();
        $response = [
            'success' => true,
            'message' => 'Driver Seats Trips',
            'data' => $data
        ];
        return $response;
    }

    public function driverSeatsTripsSchedule(Request $request, $driver_id)
    {
        $request = $request->all();
        $request['driver_id'] = $driver_id;

        $validator = Validator::make($request, [
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

        $data = $this->seatsTripRepository->driverTrips($request);

        return [
            'success' => true,
            'message' => 'Driver Seats Trips Schedule',
            'data' => $data,
        ];
    }

    public function driverLiveSeatsTrips($driver_id)
    {
        return $this->seatsTripRepository->driverLiveTrips(['driver_id' => $driver_id]);
    }

    public function seatsTripAppTransactionsDetail(Request $req, $trip_id)
    {
        $validator = Validator::make(['trip_time' => $req->trip_time],[
            'trip_time' => ['required']
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        $data = SeatsTripAppTransaction::where('trip_id', $trip_id)
            ->where('trip_time', $req->trip_time)
            ->get();

        return [
            'success' => true,
            'message' => 'Driver Live Business Trips',
            'data' => $data,
        ];
    }
}