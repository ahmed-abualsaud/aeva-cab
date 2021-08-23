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

class DriverQueriesController 
{
    public function auth()
    {
        return auth('driver')->user();
    }

    public function driver($id)
    {
        return Driver::findOrFail($id);
    }

    public function vehicle($id)
    {
        return Vehicle::findOrFail($id);
    }

    public function supervisor($id)
    {
        return Supervisor::findOrFail($id);
    }

    public function businessTrip($id)
    {
        return BusinessTrip::findOrFail($id);
    }

    public function driverSeatsTrips($driver_id)
    {
        return SeatsTrip::where('driver_id', $driver_id)->get();
    }

    public function businessTripStations($trip_id)
    {
        return BusinessTripStation::where('trip_id', $trip_id)->get();
    }

    public function seatsTripAppTransactionsDetail(Request $req, $trip_id)
    {
        return SeatsTripAppTransaction::where('trip_id', $trip_id)
        ->where('trip_time', $req->trip_time)->get();
    }
}