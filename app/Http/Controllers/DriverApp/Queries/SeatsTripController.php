<?php

namespace App\Http\Controllers\DriverApp\Queries;

use App\Repository\Queries\SeatsTripRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeatsTripController 
{
    private $seatsTripRepository;
  
    public function __construct(SeatsTripRepositoryInterface $seatsTripRepository)
    {
        $this->seatsTripRepository = $seatsTripRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function driverTrips(Request $request, $driver_id)
    {
        $request = $request->all();
        $request['driver_id'] = $driver_id;

        $validator = Validator::make($request, [
            'driver_id' => ['required', 'exists:seats_trips,driver_id'],
            'day' => ['required']
        ]);

        if ($validator->fails())
            return response()->json($validator->errors(), 500);

        return $this->seatsTripRepository->driverTrips($request);
    }

    public function driverLiveTrips($driver_id)
    {
        $validator = Validator::make(['driver_id' => $driver_id], [
            'driver_id' => ['required', 'exists:seats_trips,driver_id']
        ]);

        if ($validator->fails())
            return response()->json($validator->errors(), 500);

        return $this->seatsTripRepository->driverLiveTrips(['driver_id' => $driver_id]);
    }
}