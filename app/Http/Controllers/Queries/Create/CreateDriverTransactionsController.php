<?php /** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Controllers\Queries\Create;

use App\DriverTransaction;
use App\Http\Controllers\Controller;
use App\Http\Requests\BulkTransactionsCreateRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CreateDriverTransactionsController extends Controller
{
    /**
     * @param BulkTransactionsCreateRequest $request
     * @return JsonResponse
     */
    public function __invoke(BulkTransactionsCreateRequest $request)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $insertion_uuid = Str::orderedUuid();

        $data = $request->collect('driver_id')->transform(fn($driver_id) => [
            'driver_id'=> $driver_id,
            'type'=> $request->type,
            'amount'=> $request->amount,
            'admin_id'=> $request->admin_id,
            'admin_type'=> $request->admin_type,
            'notes'=> optional($request)->notes,
            'created_at'=> $now,
            'insertion_uuid'=> $insertion_uuid,
        ]);

        DriverTransaction::query()->insert($data->all());
        $transactions = DriverTransaction::query()->where('insertion_uuid','=',$insertion_uuid)->paginate(50);
        return dashboard_info('Transactions Created Successfully',compact('transactions'));
    }
}
