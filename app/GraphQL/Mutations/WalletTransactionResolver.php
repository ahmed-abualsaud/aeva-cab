<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\WalletTransaction;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class WalletTransactionResolver
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

            $this->updateWalletBalance($args);

            $transaction = WalletTransaction::create($input);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $this->updateWalletBalance($args);
            throw new CustomException($e->getMessage());
        }

        return $transaction;
    }

    protected function updateWalletBalance($args)
    {
        try {
            switch($args['type']) {
                case 'DEPOSIT':
                    return User::updateBalance($args['user_id'], -abs($args['amount']));
                default:
                    return User::updateBalance($args['user_id'], abs($args['amount'])); 
            }
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }
}
