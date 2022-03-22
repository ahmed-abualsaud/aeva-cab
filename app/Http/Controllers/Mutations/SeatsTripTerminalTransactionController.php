<?php

namespace App\Http\Controllers\Mutations;

use Illuminate\Http\Request;
use App\Repository\Eloquent\Controllers\SeatsTripTerminalTransactionRepository;

class SeatsTripTerminalTransactionController
{
    private $seatsTripTerminalTransactionRepository;

    public function __construct(SeatsTripTerminalTransactionRepository $seatsTripTerminalTransactionRepository)
    {
        $this->seatsTripTerminalTransactionRepository = $seatsTripTerminalTransactionRepository;
    }

    public function create(Request $req) 
    {
        try {
            $data = $this->seatsTripTerminalTransactionRepository->create($req);
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

}
