<?php

namespace Aeva\Cab\Domain\Repository\Eloquent\Mutations;

use App\Exceptions\CustomException;

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

        if (is_zero($args['costs']) && is_zero($request->remaining)) {
            $trx = new CabRequestTransaction($input);
            $trx->debt = 0;
        }

        if ($args['payment_method'] == 'Cash' &&
            str_contains(strtolower($request->history['sending']['payment_method']), 'cash') &&
            $request->remaining > 0)
        {
            $trx = $this->cash($args, $input, $request);
        }

        if ($args['payment_method'] == 'Wallet' &&
            str_contains(strtolower($request->history['sending']['payment_method']), 'wallet') &&
            $request->remaining > 0)
        {
            if ($this->cash_after_wallet) {
                $input['payment_method'] = 'Cash';
                $trx = $this->cash($args, $input, $request);
            } else {
                $trx = $this->wallet($args, $input, $request);
            }
        }

        if ($request->costs > $request->costs_after_discount && !$this->cash_after_wallet) {
            $input['costs'] = $request->discount;
            $input['payment_method'] = 'Promo Code Remaining';
            $this->model->create($input);
        }

        if(!is_zero($this->refund)) {
            $input['costs'] = $this->refund;
            $input['payment_method'] = 'Refund';
            $this->model->create($input);
        }

        if (empty($request->remaining)) {
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
        $trx = $this->model->create($input);
        $request->update(['status' => 'Completed', 'paid' => true, 'remaining' => 0]);
        $trx->debt = 0;
        $this->notifyUserOfPayment($request, $this->refund);
        return $trx;
    }

    protected function wallet($args, $input, $request)
    {
        $paid = $this->walletPay($args, $request);

        if ($paid < $args['costs']) {
            $input['costs'] = $paid;
        }

        $trx = $this->model->create($input);
        $trx->debt = $args['costs'] - $paid;

        if ($paid == $args['costs']) {
            $request->update(['status' => 'Completed', 'paid' => true, 'remaining' => 0]);
        } else {
            $request->update(['remaining' => $trx->debt]);
        }

        $this->notifyUserOfPayment($request, 0);
        return $trx;
    }

    protected function cashPay($args, $request)
    {
        $refund = $args['costs'] - $request->remaining;
        $driver_wallet = $this->cash_after_wallet? -$refund : $request->discount - $refund;
        $this->updateDriverWallet($request->driver_id, ($args['costs'] + $driver_wallet), $args['costs'], $driver_wallet);
        $this->updateUserWallet($request->user_id, $refund, 'Aevacab Refund', $args['uuid'].'-refund');
        $this->updateUserWallet($request->user_id, $args['costs'], 'Cash', $args['uuid']);
        return $refund;
    }

    protected function walletPay($args, $request)
    {
        $paid = $this->updateUserWallet($request->user_id, $args['costs'], 'Aevapay User Wallet', $args['uuid']);
        $driver_wallet = $request->discount + $paid;
        $this->updateDriverWallet($request->driver_id, $driver_wallet, 0, $driver_wallet);
        return $paid;
    }

    protected function updateUserWallet($user_id, $costs, $type, $uuid)
    {
        try {
            $user = User::findOrFail($user_id);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.user_not_found'));
        }

        if ($type == 'Aevacab Refund' && is_zero($costs)) { return; }

        if ($type == 'Aevapay User Wallet' && is_zero($user->wallet)) {
            throw new CustomException(__('lang.empty_user_wallet'));
        }

        if ($type == 'Aevapay User Wallet' && $user->wallet < $costs) {
            $costs = $user->wallet;
        }

        try {
            $this->pay([
                'user_id' => $user_id,
                'amount' => $costs,
                'type' => $type,
                'uuid' => $uuid
            ]);
        } catch (\Exception $e) {
            throw new CustomException($this->parseErrorMessage($e->getMessage(), 'status"'));
        }

        return $costs;
    }

    protected function updateDriverWallet($driver_id, $earnings, $cash, $wallet)
    {
        try {
            $stats = DriverStats::where('driver_id', $driver_id)->firstOrFail();
        } catch (\Exception $e) {
            throw new CustomException(__('lang.driver_not_found'));
        }

        if($stats->wallet + $wallet < 0) {
            throw new CustomException(__('lang.insufficient_driver_wallet_balance', ['cash_amount' => $stats->wallet + $this->costs]));
        }

        $stats->update([
            'cash' => DB::raw('cash + '.$cash),
            'wallet' => DB::raw('wallet + '.$wallet),
            'earnings' => DB::raw('earnings + '.$earnings)
        ]);

        DriverLog::log([
            'driver_id' => $driver_id,
            'cash' => $cash,
            'wallet' => $wallet,
            'earnings' => $earnings
        ]);
    }

    protected function notifyUserOfPayment($request, $refund)
    {
        $socket_request = $request->toArray();
        $socket_request['refund'] = $refund;
        SendPushNotification::dispatch(
            $this->userToken($socket_request['user_id']),
            __('lang.ride_completed_body'),
            __('lang.ride_completed'),
            ['view' => 'RideCompleted', 'id' => $socket_request['id']]
        );

        broadcast(new CabRequestStatusChanged($socket_request));
    }

    protected function parseErrorMessage($err_mesg, $needle)
    {
        $index = strpos($err_mesg, $needle);
        if($index) {
            return json_decode(substr($err_mesg, $index - 2))->message;
        }
        return $err_mesg;
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
            'cashout_remaining' => -$args['amount']
        ]);

        try {
            $this->cashout([
                'reference_number' => $args['reference_number']
            ]);
        } catch (\Exception $e) {
            throw new CustomException($this->parseErrorMessage($e->getMessage(), 'success'));
        }

        $cashout = $this->model->create([
            'driver_id' => $args['driver_id'],
            //'merchant_id' => $args['merchant_id'],
            //'merchant_name' => $args['merchant_name'],
            'costs' => $args['amount'],
            'payment_method' => 'Cashout',
            'uuid' => Str::orderedUuid()
        ]);

        $stats->update([
            'wallet' => DB::raw('wallet - '.$args['amount']),
            'earnings' => DB::raw('earnings - '.$args['amount'])
        ]);

        return $cashout;
    }
}
