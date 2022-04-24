<?php

namespace Aeva\Seats\Application\Http\Controllers\Queries;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use Aeva\Seats\Domain\Models\SeatsTrip;
use Aeva\Seats\Domain\Models\SeatsLineStation;
use Aeva\Seats\Domain\Models\SeatsTripAppTransaction;
use Aeva\Seats\Domain\Repository\Queries\SeatsTripRepositoryInterface;

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

    public function seatsTripLineStations($line_id)
    {
        $data = SeatsLineStation::where('line_id', $line_id)->get();
        $response = [
            'success' => true,
            'message' => 'Seats Trip Line Stations',
            'data' => $data
        ];

        return $response;
    }
}