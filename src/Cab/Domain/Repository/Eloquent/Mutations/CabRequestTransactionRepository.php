<?php

namespace Aeva\Cab\Domain\Repository\Eloquent\Mutations;

use App\Exceptions\CustomException;

use App\User;
use App\Driver;

use Aeva\Cab\Domain\Models\CabRequest;
use Aeva\Cab\Domain\Models\CabRequestTransaction;

use Aeva\Cab\Domain\Repository\Eloquent\BaseRepository;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class CabRequestTransactionRepository extends BaseRepository
{
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

        $input =  Arr::except($args, ['directive']);
        $input['user_id'] = $request->user_id;
        $input['driver_id'] = $request->driver_id;

        if ($args['payment_method'] == 'Cash') {
            $this->cashPay($args, $request);
            $request->update([ 'status' => 'Completed', 'paid' => true]);
            $trx = $this->model->create($input);
            $trx->debt = 0;
            return $trx;
        }

        if ($args['payment_method'] == 'Wallet') {
            $paid = $this->walletPay($args, $request);
            if ($paid < $args['costs']) {
                $input['costs'] = $paid;
            }
            $trx = $this->model->create($input);
            $trx->debt = $args['costs'] - $paid;
            return $trx;
        }
    }

    public function destroy(array $args)
    {
        return $this->model->whereIn('id', $args['id'])->delete();
    }

    protected function cashPay($args, $request)
    {
        if($args['costs'] > $request->costs) {
            $this->updateUserWallet($request->user_id, ($args['costs'] - $request->costs), '+');
        }

        $this->updateDriverWallet($request->driver_id, $args['costs'], 0);
    }

    protected function walletPay($args, $request)
    {
        $paid = $this->updateUserWallet($request->user_id, $args['costs'], '-');
        $this->updateDriverWallet($request->driver_id, $paid, $paid);
        return $paid;
    }

    protected function updateUserWallet($user_id, $costs, $sign)
    {
        try {
            $user = User::findOrFail($user_id);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.user_not_found'));
        }

        if ($sign == '-') {
            $paid = $costs;
            if ($user->wallet < $costs) {
                $paid = $user->wallet;
            }

            // decrement the user wallet by $paid

            return $paid;
        }

        if ($sign == '+') {

            // increment the user wallet by $costs

            return $costs;
        }
    }

    protected function updateDriverWallet($driver_id, $costs, $balance)
    {
        try {
            $driver = Driver::findOrFail($driver_id);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.driver_not_found'));
        }

        if ($costs != 0) {$driver->increment('earnings', $costs);}
        if ($balance != 0) {$driver->increment('balance', $balance);}
    }
}