<?php

namespace App\Repository\Eloquent\Mutations;

use App\DriverStats;
use App\DriverTransaction;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use App\Repository\Eloquent\BaseRepository;

class DriverTransactionRepository extends BaseRepository
{
    public function __construct(DriverTransaction $model)
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
            throw new CustomException(
                __('lang.create_trnx_failed'),
                'customValidation'
            ); 
        }

        $transaction->created_at = date('Y-m-d H:i:s');

        return $transaction;
    }

    protected function updateBalance($args)
    {
        try {
            switch($args['type']) {
                case 'Wallet Deposit':
                    return DriverStats::where('driver_id', $args['driver_id'])->increment('wallet', $args['amount']);
                case 'Wallet Withdraw':
                    return DriverStats::where('driver_id', $args['driver_id'])->decrement('wallet', $args['amount']);
                case 'Cashout':
                    return DriverStats::where('driver_id', $args['driver_id'])->decrement('wallet', $args['amount']);
            }
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }
}
