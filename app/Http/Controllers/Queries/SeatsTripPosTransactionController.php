<?php

namespace App\Http\Controllers\Queries;

use App\Repository\Queries\SeatsTripPosTransactionRepositoryInterface;

class SeatsTripPosTransactionController
{
    
    private $seatsTripPosTransactionRepository;
  
    public function __construct(SeatsTripPosTransactionRepositoryInterface $seatsTripPosTransactionRepository)
    {
        $this->seatsTripPosTransactionRepository = $seatsTripPosTransactionRepository;
    }

    public function driverMaxSerial($driver_id)
    {
        return [
            'success' => true,
            'message' => 'Driver Max Serial',
            'data' => $this->seatsTripPosTransactionRepository->driverMaxSerial(['driver_id' => $driver_id])
        ];
    }

}