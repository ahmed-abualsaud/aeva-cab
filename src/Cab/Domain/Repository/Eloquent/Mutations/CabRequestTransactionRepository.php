<?php

namespace Aeva\Cab\Domain\Repository\Eloquent\Mutations;

use App\Exceptions\CustomException;

use App\Helpers\TraceEvents;
use App\User;
use App\Driver;
use App\DriverLog;
use App\DriverStats;

use App\Jobs\SendPushNotification;

use Aeva\Cab\Domain\Models\CabRequest;
use Aeva\Cab\Domain\Models\CabRequestTransaction;

use Aeva\Cab\Domain\Traits\CabRequestHelper;
use Aeva\Cab\Domain\Traits\HandleDeviceTokens;
use Aeva\Cab\Domain\Events\CabRequestStatusChanged;
use Aeva\Cab\Domain\Repository\Eloquent\BaseRepository;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class CabRequestTransactionRepository extends BaseRepository
{
    use CabRequestHelper;
    use HandleDeviceTokens;

    protected $costs;
    protected $refund;
    protected $cash_after_wallet;

    public function __construct(CabRequestTransaction $model)
    {
        parent::__construct($model);

    }

    public function create(array $args)
    {
        try {
            $request = CabRequest::findOrFail($args['request_id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.request_not_found'));
        }

        if ($request->paid) {
            throw new CustomException(__('lang.request_already_paid'));
        }

        if (is_zero($args['costs']) && $request->remaining > 0) {
            throw new CustomException(__('lang.amount_can_not_be_zero'));
        }

        if ($args['costs'] < $request->remaining) {
            throw new CustomException(__('lang.amount_paid_less_than_amount_requested'));
        }

        $args['uuid'] = Str::orderedUuid();
        $input =  Arr::except($args, ['directive']);
        $input['user_id'] = $request->user_id;
        $input['driver_id'] = $request->driver_id;

        $this->cash_after_wallet = ($request->costs_after_discount > $request->remaining);
        $this->costs = $this->cash_after_wallet? $request->remaining : $request->costs;

        if (($args['payment_method'] == 'Cash' || ($args['payment_method'] == 'Wallet' && $this->cash_after_wallet)) && $request->remaining > 0) {
            if ($this->model->where('payment_method', 'Cash')->where('request_id', $input['request_id'])->exists())
            {
                throw new CustomException(__('lang.trx_exists'));
            }

            $trx = $this->cash($args, $input, $request);
        }

        if(!is_zero($this->refund)) {
            $input['costs'] = $this->refund;
            $input['payment_method'] = 'Refund';
            $this->model->create($input);
        }

        if (is_zero($request->remaining)) {
            $this->updateDriverStatus($request->driver_id, 'Online');
        }

        if (!empty($trx)) {
            return $trx;
        }

        throw new CustomException(__('lang.payment_method_does_not_match'));
    }

    protected function cash($args, $input, $request)
    {
        $this->refund = $this->cashPay($args, $request);
        $input['payment_method'] = 'Cash';
        $trx = $this->model->create($input);
        $request->update(['status' => 'Completed', 'paid' => true, 'remaining' => 0]);
        trace(TraceEvents::COMPLETE_CAB_REQUEST,$request->id);
        $trx->debt = 0;
        $this->notifyUserOfPayment($request, $this->refund);
        return $trx;
    }

    protected function cashPay($args, $request)
    {
        $refund = $args['costs'] - $request->remaining;
        $driver_wallet = $this->cash_after_wallet? -$refund : $request->discount - $refund;
        $this->updateDriverWallet($request->driver_id, ($args['costs'] + $driver_wallet), $args['costs'], $driver_wallet, $this->costs);
        $this->updateUserWallet($request->user_id, $refund, 'Aevacab Refund', $args['uuid'].'-refund');
        $this->updateUserWallet($request->user_id, $args['costs'], 'Cash', $args['uuid']);
        return $refund;
    }

    public function destroy(array $args)
    {
        return $this->model->whereIn('id', $args['id'])->delete();
    }

    public function confirmCashout(array $args)
    {
        try {
            $driver = Driver::findOrFail($args['driver_id']);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.driver_not_found'));
        }

        $stats = DriverStats::where('driver_id', $args['driver_id'])->first();
        if($stats->wallet < $args['amount']) {
            throw new CustomException(__('lang.insufficient_balance'));
        }

        DriverLog::log([
            'driver_id' => $args['driver_id'],
            'cashout_amount' => $args['amount']
        ]);

        try {
            $response = $this->cashout([
                'reference_number' => $args['reference_number']
            ])->json();
        } catch (\Exception $e) {
            throw new CustomException($this->parseErrorMessage($e->getMessage(), 'success'));
        }

        Log::info($response);

        if ( !$this->successResponseFilter($args, $response) ) {
            return null;
        }

        $trx_type = 'Cashout';
        if (array_key_exists('type', $args) && $args['type']) {
            $trx_type = $args['type'];
        }

        $cashout = $this->model->create([
            'driver_id' => $args['driver_id'],
            'merchant_name' => $args['merchant_name'],
            'costs' => $args['amount'],
            'payment_method' => $trx_type,
            'reference_number' => $args['reference_number'],
            'uuid' => Str::orderedUuid()
        ]);

        $stats->update([
            'wallet' => DB::raw('wallet - '.$args['amount']),
            'earnings' => DB::raw('earnings - '.$args['amount'])
        ]);

        trace(TraceEvents::CASHOUT);

        $cashout->wallet = DriverStats::select('wallet')->where('driver_id', $args['driver_id'])->first()->wallet;

        return $cashout;
    }

    private function successResponseFilter($args, $response)
    {
        return array_key_exists('success', $response) && $response['success'] &&
            array_key_exists('code', $response) && $response['code'] == 200 &&
            array_key_exists('data', $response) &&
            array_key_exists('amount', $response['data']) && $response['data']['amount'] == $args['amount'];
            //array_key_exists('driverId', $response['data']) && $response['data']['driverId'] == $args['driver_id'];
    }
}
