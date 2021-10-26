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

}