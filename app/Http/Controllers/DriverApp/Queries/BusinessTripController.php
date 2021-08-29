<?php

namespace App\Http\Controllers\DriverApp\Queries;

use App\Repository\Queries\BusinessTripRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\HandleValidatorMessages;

class BusinessTripController 
{
    use HandleValidatorMessages;

    private $businessTripRepository;
  
    public function __construct(BusinessTripRepositoryInterface $businessTripRepository)
    {
        $this->businessTripRepository = $businessTripRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function driverTrips(Request $request, $driver_id)
    {
        $request = $request->all();
        $request['driver_id'] = $driver_id;

        $validator = Validator::make($request,[
            'driver_id' => ['required', 'exists:business_trips,driver_id'],
            'day' => ['required']
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);

        return $this->businessTripRepository->driverTrips($request);
    }

    public function driverLiveTrips($driver_id)
    {
        $validator = Validator::make(['driver_id' => $driver_id],[
            'driver_id' => ['required', 'exists:business_trips,driver_id'],
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);

        return $this->businessTripRepository->driverLiveTrips(['driver_id' => $driver_id]);
    }
}