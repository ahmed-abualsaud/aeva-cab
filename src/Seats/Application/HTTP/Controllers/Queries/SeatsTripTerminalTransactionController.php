<?php

namespace Qruz\Seats\Application\Http\Controllers\Queries;

use Illuminate\Http\Request;

use Qruz\Seats\Domain\Repository\Eloquent\Mutations\SeatsTripTerminalTransactionRepository;

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
