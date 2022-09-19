<?php

namespace Aeva\Cab\Application\Http\Controllers\Mutations;

use Aeva\Cab\Domain\Repository\Eloquent\Mutations\CabRequestTransactionRepository;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
            'amount' => ['required'],
            'merchant_name' => ['required'],
            'reference_number' => ['required'],
            'type' => ['required', Rule::in(['Cashout', 'Scan And Pay'])]
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
        }

        try {
            $data = $this->cabRequestTransactionRepository->confirmCashout($req->all());
            return [
                'success' => true,
                'data' => $data,
                'message' => 'Cashout Process Confirmed Successfully'
            ];
       } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
       }
    }
}
