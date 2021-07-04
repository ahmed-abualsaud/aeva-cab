<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\UserTransaction;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class UserTransactionResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        DB::beginTransaction();
        try {
            $input = collect($args)->except(['directive'])->toArray();

            $this->updateBalance($args);

            $transaction = UserTransaction::create($input);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $this->updateBalance($args);
            throw new CustomException(__('lang.update_failed'));
        }

        return $transaction;
    }

    protected function updateBalance($args)
    {
        try {
            switch($args['type']) {
                case 'WALLET_DEPOSIT':
                    return User::updateWallet($args['user_id'], -abs($args['amount']));
                case 'WALLET_WITHDRAW':
                    return User::updateWallet($args['user_id'], abs($args['amount'])); 
                case 'INSURANCE_DEPOSIT':
                    return User::updateInsurance($args['user_id'], -abs($args['amount']));
                case 'INSURANCE_WITHDRAW':
                    return User::updateInsurance($args['user_id'], abs($args['amount'])); 
            }
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }
}
