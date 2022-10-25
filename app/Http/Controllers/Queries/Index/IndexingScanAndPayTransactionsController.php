<?php

namespace App\Http\Controllers\Queries\Index;

use Aeva\Cab\Domain\Models\CabRequestTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class IndexingScanAndPayTransactionsController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function __invoke()
    {
        $scan_and_pay_transactions = CabRequestTransaction::scanAndPay()->paginate(50);
        return dashboard_info('Scan And Pay Transactions',compact('scan_and_pay_transactions'));
    }
}
