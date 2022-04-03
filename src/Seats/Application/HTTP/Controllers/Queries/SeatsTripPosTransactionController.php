<?php

namespace Qruz\Seats\Application\Http\Controllers\Queries;

use Illuminate\Http\Request;

use Qruz\Seats\Domain\Repository\Queries\SeatsTripPosTransactionRepositoryInterface;

class SeatsTripPosTransactionController
{
    
    private $seatsTripPosTransactionRepository;
  
    public function __construct(SeatsTripPosTransactionRepositoryInterface $seatsTripPosTransactionRepository)
    {
        $this->seatsTripPosTransactionRepository = $seatsTripPosTransactionRepository;
    }

    public function export(Request $req) 
    {
        return $this->seatsTripPosTransactionRepository->export($req);
    }

    public function vehicleMaxSerial($vehicle_id) 
    {
        $response = [
            'status' => true,
            'data' => $this->seatsTripPosTransactionRepository->vehicleMaxSerial($vehicle_id)
        ];

        return response()->json($response, 200);
    }

    public function driverReport(Request $req) 
    {
        return $this->seatsTripPosTransactionRepository->driverReport($req);
    }
}