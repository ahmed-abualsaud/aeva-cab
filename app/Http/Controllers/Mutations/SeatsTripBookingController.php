<?php

namespace App\Http\Controllers\Mutations;

use App\Repository\Eloquent\Mutations\SeatsTripBookingRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SeatsTripBookingController 
{

    private $seatsTripBookingRepository;

    public function __construct(SeatsTripBookingRepository $seatsTripBookingRepository)
    {
        $this->seatsTripBookingRepository = $seatsTripBookingRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => ['required'],
            'status' => Rule::in(['CONFIRMED', 'CANCELLED', 'MISSED', 'COMPLETED'])
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        return $this->seatsTripBookingRepository->update($request->all());
    }
}