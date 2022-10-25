<?php

namespace App\Http\Controllers\Queries\Index;

use Aeva\Cab\Domain\Models\CabRequestTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class IndexingCabTransactionsController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function __invoke()
    {
        $cab_transactions = CabRequestTransaction::indexTrxs()->paginate(50);
        return dashboard_info('Cab Transactions',compact('cab_transactions'));
    }
}
