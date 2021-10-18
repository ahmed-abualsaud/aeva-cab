<?php

namespace App\Http\Controllers\Mutations;

use App\Repository\Eloquent\Mutations\SeatsTripPosTransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeatsTripPosTransactionController 
{

    private $SeatsTripPosTransactionRepository;

    public function __construct(SeatsTripPosTransactionRepository $SeatsTripPosTransactionRepository)
    {
        $this->SeatsTripPosTransactionRepository = $SeatsTripPosTransactionRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'partner_id' => ['required'],
            'driver_id' => ['required'],
            'vehicle_id' => ['required'],
            'tickets' => ['required'],
            'amount' => ['required']
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        $response = [
            'success' => true,
            'message' => 'Transaction created successfully',
            'data' => $this->SeatsTripPosTransactionRepository->create($request->all())
        ];

        return $response;
    }
}