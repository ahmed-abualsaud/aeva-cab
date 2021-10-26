<?php

namespace App\Http\Controllers\Queries;

use Illuminate\Http\Request;
use App\Repository\Queries\SeatsTripPosTransactionRepositoryInterface;

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

}