<?php

namespace App\GraphQL\Mutations;

use App\User;
use Illuminate\Support\Carbon;
use App\BusinessTripSubscription;
use Illuminate\Support\Facades\DB;
use App\BusinessTripAppTransaction;
use App\Exceptions\CustomException;

class BusinessTripAppTransactionResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        DB::beginTransaction();
        try {
            $this->updateSubscription($args);
            if ($args['payment_method'] === 'CASH') $this->cashPay($args);
            $transaction = $this->saveTransaction($args);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($args['payment_method'] === 'CARD') $this->cardPay($args);
            throw new CustomException($e->getMessage());
        }

        return $transaction;
    }

    public function destroy($_, array $args)
    {
        return BusinessTripAppTransaction::whereIn('id', $args['id'])->delete();
    }

    protected function saveTransaction($args)
    {
        try {
            $input = collect($args)->except(['directive'])->toArray();

            return BusinessTripAppTransaction::create($input);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.create_trnx_failed'));
        }
    }

    protected function updateSubscription($args)
    {
        try {
            return BusinessTripSubscription::where('trip_id', $args['trip_id'])
                ->where('user_id', $args['user_id'])
                ->update(['due_date' => Carbon::parse($args['due_date'])->addMonth()]);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.update_failed'));
        }
    }

    protected function cashPay($args)
    {
        try {
            $wallet = auth('user')->user()->wallet_balance;
            if ($wallet >= $args['amount']) {
                return User::updateWallet($args['user_id'], $args['amount']);
            } else {
                throw new CustomException(__('lang.insufficient_balance'));
            }
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    protected function cardPay($args)
    {
        try {
            User::updateWallet($args['user_id'], -abs($args['amount']));
        } catch (\Exception $e) {
            throw new CustomException(__('lang.update_failed'));
        }
    }
}
