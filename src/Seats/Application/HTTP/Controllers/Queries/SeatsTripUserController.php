<?php

namespace Qruz\Seats\Application\Http\Controllers\Queries;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use Qruz\Seats\Domain\Repository\Eloquent\Queries\SeatsTripUserRepository;

class SeatsTripUserController
{

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
            'trip_time' => ['required'],
            'status' => ['required', Rule::in(['PICK_UP', 'DROP_OFF'])]
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        return $this->seatsTripUserRepository->invoke($request);
    }
}