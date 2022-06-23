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

        $sum = $this->model->where('request_id', $request->id)
            ->selectRaw('request_id, sum(costs) as amount')
            ->groupBy('request_id')->first();

        if (!is_null($sum) && $sum->amount >= $request->costs) {
            throw new CustomException(__('lang.request_already_paid'));
        }

        if ($args['costs'] < $request->costs) {
            throw new CustomException(__('lang.amount_paid_less_than_amount_requested'));
        }

        $args['uuid'] = Str::orderedUuid();
        $input =  Arr::except($args, ['directive']);
        $input['user_id'] = $request->user_id;
        $input['driver_id'] = $request->driver_id;

        $payment_method = strtolower($request->history['sending']['payment_method']);
        if ($args['payment_method'] == 'Cash' && str_contains($payment_method, 'cash')) {
            $refund = $this->cashPay($args, $request);
            $request->update(['status' => 'Completed', 'paid' => true]);
            $trx = $this->model->create($input);
            $trx->debt = 0;
            $request->refund = $refund;
            $this->notifyUserOfPayment($request);
            return $trx;
        }

        if ($args['payment_method'] == 'Wallet' && str_contains($payment_method, 'wallet')) {
            $paid = $this->walletPay($args, $request);
            if ($paid < $args['costs']) {
                $input['costs'] = $paid;
            } 
            
            if ($paid == $args['costs']) {
                $request->update(['status' => 'Completed', 'paid' => true]);
            }

            $trx = $this->model->create($input);
            $trx->debt = $args['costs'] - $paid;
            $request->refund = 0;
            $this->notifyUserOfPayment($request);
            return $trx;
        }
        
        throw new CustomException(__('lang.payment_method_does_not_match'));
    }

    public function destroy(array $args)
    {
        return $this->model->whereIn('id', $args['id'])->delete();
    }

    protected function cashPay($args, $request)
    {
        $refund = 0;
        $costs = $args['costs'];
        if($args['costs'] > $request->costs) {
            $refund = ($args['costs'] - $request->costs);
            $costs = $request->costs;
            $this->updateUserWallet($request->user_id, $refund, 'Refund', $args['uuid'].'-refund');
        }

        $this->updateUserWallet($request->user_id, $costs, 'Cash', $args['uuid']);
        $this->updateDriverWallet($request->driver_id, $args['costs'], $args['costs'], ($request->costs - $args['costs']));
        return $refund;
    }

    protected function walletPay($args, $request)
    {
        $paid = $this->updateUserWallet($request->user_id, $args['costs'], 'Aevapay User Wallet', $args['uuid']);
        $this->updateDriverWallet($request->driver_id, $paid, 0, $paid);
        return $paid;
    }

    protected function updateUserWallet($user_id, $costs, $type, $uuid)
    {
        try {
            $user = User::findOrFail($user_id);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.user_not_found'));
        }

        $paid = $costs;
        if ($type == 'Aevapay User Wallet') {
            if ($user->wallet < $costs) {
                $paid = $user->wallet;
            }
        }

        $this->pay([
            'user_id' => $user_id,
            'amount' => $paid,
            'type' => $type,
            'uuid' => $uuid
        ]);

        return $paid;
    }

    protected function updateDriverWallet($driver_id, $earnings, $cash, $wallet)
    {
        DriverStats::where('driver_id', $driver_id)->update([
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

    protected function notifyUserOfPayment($request)
    {
        SendPushNotification::dispatch(
            $this->userToken($request->user_id),
            __('lang.ride_completed_body'),
            __('lang.ride_completed'),
            ['view' => 'RideCompleted', 'id' => $request->id]
        );

        broadcast(new CabRequestStatusChanged($request));
    }
}