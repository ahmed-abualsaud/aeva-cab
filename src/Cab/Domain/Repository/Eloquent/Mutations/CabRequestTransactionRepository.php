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

        $input =  Arr::except($args, ['directive', 'paid']);
        $input['user_id'] = $request->user_id;
        $input['driver_id'] = $request->driver_id;

        switch($args['payment_method']) 
        {
            case 'Cash':
                $this->cashPay($args, $request);
                break;
            case 'Wallet':
                $this->walletPay($args, $request);
        }

        $request->update([ 'status' => 'Completed', 'paid' => true]);
        return $this->model->create($input);
    }

    public function destroy(array $args)
    {
        return $this->model->whereIn('id', $args['id'])->delete();
    }

    protected function cashPay($args, $request)
    {
        $wallet_money = $args['costs'] - $args['paid'];
        $this->updateUserWallet($request->user_id, $wallet_money, '-');
        $this->updateDriverWallet($request->driver_id, $args['costs'], $wallet_money);
    }

    protected function walletPay($args, $request)
    {
        $this->updateUserWallet($request->user_id, $args['costs'], '-');
        $this->updateDriverWallet($request->driver_id, $args['costs'], $args['costs']);
    }

    protected function updateUserWallet($user_id, $costs, $sign)
    {
        try {
            $user = User::findOrFail($user_id);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.user_not_found'));
        }

        if($sign == '-') {
            // if ( $user->wallet < $costs ) {
            //     throw new CustomException(__('lang.insufficient_balance'));
            // }
            // decrement the user wallet by $costs
        }
        
        if($sign == '+') {
            // increment the user wallet by $costs
        }
    }

    protected function updateDriverWallet($driver_id, $costs, $balance)
    {
        try {
            $driver = Driver::findOrFail($driver_id);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.driver_not_found'));
        }
        
        $driver->increment('earnings', $costs);
        $driver->increment('balance', $balance);
    }
}