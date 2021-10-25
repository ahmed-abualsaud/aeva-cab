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

    public function vehicleMaxSerial($vehicle_id)
    {
        return [
            'success' => true,
            'message' => 'Vehicle Max Serial',
            'data' => $this->seatsTripPosTransactionRepository->vehicleMaxSerial(['vehicle_id' => $vehicle_id])
        ];
    }

}