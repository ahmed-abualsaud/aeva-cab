<?php

namespace App\Http\Controllers\Queries\Index;

use Aeva\Cab\Domain\Models\CabRequestTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class IndexingCashOutTransactionsController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function __invoke()
    {
        $cashout_transactions = CabRequestTransaction::cashOut()->paginate(50);
        return dashboard_info('Cash Out Transactions',compact('cashout_transactions'));
    }
}
