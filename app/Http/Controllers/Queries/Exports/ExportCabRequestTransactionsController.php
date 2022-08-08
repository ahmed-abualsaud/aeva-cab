<?php

namespace App\Http\Controllers\Queries\Exports;

use Aeva\Cab\Domain\Models\CabRequestTransaction;
use App\Exports\CabRequestTransactionsExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ExportCabRequestTransactionsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request)
    {
        return (new CabRequestTransactionsExport(CabRequestTransaction::searchApplied()))->download();
    }
}
