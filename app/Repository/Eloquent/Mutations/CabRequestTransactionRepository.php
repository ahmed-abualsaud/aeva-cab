<?php

namespace App\Repository\Eloquent\Mutations;

use App\User;
use App\CabRequest;
use App\CabRequestTransaction;
use App\Exceptions\CustomException;
use App\Repository\Eloquent\BaseRepository;

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

        switch($args['payment_method']) 
        {
            case 'CARD':
                $this->cardPay($args, $request);
                break;
            case 'WALLET':
                $this->walletPay($args, $request);
        }
        $request->update(['paid' => true]);
        return $this->model->create($input);
    }

    public function confirmCashPayment(array $args)
    {
        try {
            return CabRequest::findOrFail($args['request_id'])->update(['paid' => true]);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.request_not_found'));
        }
    }

    public function destroy(array $args)
    {
        return $this->model->whereIn('id', $args['id'])->delete();
    }

    protected function cardPay($args, $request)
    {
        $extra = $args['amount'] - $args['paid'];
        if ($extra) {
            $this->updateWallet($args['user_id'], $extra);
        }
        //$request->update(['paid' => true]);
    }

    protected function walletPay($args, $request)
    {
        $this->updateWallet($args['user_id'], $args['amount']);
        //$request->update(['paid' => true]);
    }

    protected function updateWallet($user_id, $amount)
    {
        try {
            $user = User::findOrFail($user_id);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.user_not_found'));
        }

        if ( $user->wallet_balance < $amount ) {
            throw new CustomException(__('lang.insufficient_balance'));
        }
        $user->updateWallet($user_id, $amount);
    }
}