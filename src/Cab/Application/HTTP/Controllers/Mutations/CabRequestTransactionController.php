<?php

namespace Aeva\Cab\Application\Http\Controllers\Mutations;

use Aeva\Cab\Domain\Repository\Eloquent\Mutations\CabRequestTransactionRepository;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class CabRequestTransactionController 
{
    private $cabRequestTransactionRepository;

    public function __construct(CabRequestTransactionRepository $cabRequestTransactionRepository)
    {
        $this->cabRequestTransactionRepository = $cabRequestTransactionRepository;
    }

    public function confirmCashout(Request $req) 
    {
        $validator = Validator::make($req->all(), [
            'driver_id' => ['required'],
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
            $data = $this->cabRequestTransactionRepository->confirmCashout($req->all());
            $response = [
                'success' => true,
                'data' => $data,
                'message' => 'Cashout Process Confirmed Successfully'
            ];

            return $response;
       } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
            return response()->json($response, 400);
       }
    }
}