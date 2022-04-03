<?php

namespace Qruz\Seats\Application\Http\Controllers\Mutations;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Qruz\Seats\Domain\Repository\Eloquent\Mutations\SeatsTripPosTransactionRepository;

class SeatsTripPosTransactionController 
{

    private $SeatsTripPosTransactionRepository;

    public function __construct(SeatsTripPosTransactionRepository $SeatsTripPosTransactionRepository)
    {
        $this->SeatsTripPosTransactionRepository = $SeatsTripPosTransactionRepository;
    }

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

        try {
            $data = $this->SeatsTripPosTransactionRepository->create($request->all());
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }

        $response = [
            'success' => true,
            'message' => 'Transaction created successfully',
            'data' => $data
        ];

        return $response;
    }

    public function bulkCreate(Request $request)
    {
        try {
            $this->SeatsTripPosTransactionRepository->bulkCreate($request->all());
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }

        $response = [
            'success' => true,
            'message' => 'Transactions created successfully'
        ];

        return $response;
    }
}