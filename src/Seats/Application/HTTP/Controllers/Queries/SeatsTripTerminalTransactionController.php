<?php

namespace Aeva\Seats\Application\Http\Controllers\Queries;

use Illuminate\Http\Request;

use Aeva\Seats\Domain\Repository\Eloquent\Mutations\SeatsTripTerminalTransactionRepository;

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
