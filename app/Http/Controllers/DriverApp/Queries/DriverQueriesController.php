<?php

namespace App\Http\Controllers\DriverApp\Queries;

use App\Driver;
use App\Vehicle;
use App\Supervisor;
use App\SeatsTrip;
use App\BusinessTrip;
use App\BusinessTripStation;
use App\SeatsTripAppTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\HandleValidatorMessages;

class DriverQueriesController 
{
    use HandleValidatorMessages;

    public function auth()
    {
        return auth('driver')->user();
    }

    public function driver($id)
    {
        $validator = Validator::make(['id' => $id],[
            'id' => ['required', 'exists:drivers,id']
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);

        return Driver::findOrFail($id);
    }

    public function vehicle($id)
    {
        $validator = Validator::make(['id' => $id],[
            'id' => ['required', 'exists:vehicles,id']
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);

        return Vehicle::findOrFail($id);
    }

    public function supervisor($id)
    {
        $validator = Validator::make(['id' => $id],[
            'id' => ['required', 'exists:supervisors,id']
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);

        return Supervisor::findOrFail($id);
    }

    public function businessTrip($id)
    {
        $validator = Validator::make(['id' => $id],[
            'id' => ['required', 'exists:business_trips,id']
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);

        return BusinessTrip::findOrFail($id);
    }

    public function driverSeatsTrips($driver_id)
    {
        $validator = Validator::make(['driver_id' => $driver_id],[
            'driver_id' => ['required', 'exists:seats_trips,driver_id']
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);

        return SeatsTrip::where('driver_id', $driver_id)->get();
    }

    public function businessTripStations($trip_id)
    {
        $validator = Validator::make(['trip_id' => $trip_id],[
            'trip_id' => ['required', 'exists:business_trip_stations,trip_id']
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);

        return BusinessTripStation::where('trip_id', $trip_id)->get();
    }

    public function seatsTripAppTransactionsDetail(Request $req, $trip_id)
    {
        $validator = Validator::make(['trip_id' => $trip_id, 'trip_time' => $req->trip_time],[
            'trip_id' => ['required', 'exists:seats_trip_app_transactions,trip_id'],
            'trip_time' => ['required', 'exists:seats_trip_app_transactions,trip_id']
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);

        return SeatsTripAppTransaction::where('trip_id', $trip_id)
        ->where('trip_time', $req->trip_time)->get();
    }
}