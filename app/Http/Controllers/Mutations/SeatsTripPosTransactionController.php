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

    public function create(Request $request)
    {
        try {
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
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 400);
        }
    }

    public function bulkCreate(Request $request)
    {
        try {
            $this->SeatsTripPosTransactionRepository->bulkCreate($request->all());
    
            $response = [
                'success' => true,
                'message' => 'Transactions created successfully'
            ];

            return $response;
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 400);
        }
    }
}