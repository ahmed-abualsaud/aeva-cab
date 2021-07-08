<?php

namespace App\Repository\Eloquent\Mutations;

use App\User;
use App\UserTransaction;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use App\Repository\Eloquent\BaseRepository;

class UserTransactionRepository extends BaseRepository
{
    public function __construct(UserTransaction $model)
    {
        parent::__construct($model);
    }

    public function create(array $args)
    {
        DB::beginTransaction();
        try {
            $input = collect($args)->except(['directive'])->toArray();

            $this->updateBalance($args);

            $transaction = $this->model->create($input);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new CustomException($e->getMessage());
        }

        $transaction->created_at = date('Y-m-d H:i:s');

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
