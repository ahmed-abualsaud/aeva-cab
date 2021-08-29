<?php

namespace App\Http\Controllers\Queries;

use Illuminate\Http\Request;
use App\Repository\Eloquent\Controllers\SeatsTripTerminalTransactionRepository;

class SeatsTripTerminalTransactionController
{
    private $seatsTripTerminalTransactionRepository;

    public function __construct(SeatsTripTerminalTransactionRepository $seatsTripTerminalTransactionRepository)
    {
        $this->seatsTripTerminalTransactionRepository = $seatsTripTerminalTransactionRepository;
    }

    public function export(Request $req) 
    {
        return $this->seatsTripTerminalTransactionRepository->export($req);
    }

}
