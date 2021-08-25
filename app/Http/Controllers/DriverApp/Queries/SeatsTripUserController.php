<?php

namespace App\Http\Controllers\DriverApp\Queries;

use App\Repository\Eloquent\Queries\SeatsTripUserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\HandleValidatorMessages;

class SeatsTripUserController
{
    use HandleValidatorMessages;

    private $seatsTripUserRepository;

    public function __construct(SeatsTripUserRepository $seatsTripUserRepository)
    {
        $this->seatsTripUserRepository = $seatsTripUserRepository;
    }
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function users(Request $request, $trip_id)
    {
        $request = $request->all();
        $request['trip_id'] = $trip_id;

        $validator = Validator::make($request, [
            'trip_id' => ['required', 'exists:seats_trip_bookings,trip_id'],
            'trip_time' => ['required', 'exists:seats_trip_bookings,trip_time'],
            'status' => ['required', Rule::in(['PICK_UP', 'DROP_OFF'])],
            'station_id' => ['exists:seats_line_stations,id']
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);

        return $this->seatsTripUserRepository->invoke($request);
    }
}