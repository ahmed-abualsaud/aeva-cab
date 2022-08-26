<?php

namespace App\Http\Controllers\Queries\Exports;

use Aeva\Cab\Domain\Models\CabRequestTransaction;
use App\Exports\CashOutTransactionsExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportCashOutTransactionsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request)
    {
        return (new CashOutTransactionsExport(CabRequestTransaction::cashOut()))->download();
    }

}
