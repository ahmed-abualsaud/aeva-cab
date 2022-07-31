<?php

namespace App\Http\Controllers\Queries\Exports;

use App\DriverTransaction;
use App\Exports\DriverTransactionsExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExportDriverTransactionsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request)
    {
        return (new DriverTransactionsExport(DriverTransaction::searchApplied()))->download();
    }
}
