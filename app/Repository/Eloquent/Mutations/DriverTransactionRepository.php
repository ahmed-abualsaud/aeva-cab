<?php

namespace App\Repository\Eloquent\Mutations;

use App\DriverStats;
use App\DriverTransaction;
use App\Exceptions\CustomException;
use App\Repository\Eloquent\BaseRepository;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            $stats = DriverStats::where('driver_id', $args['driver_id'])->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.driver_not_found'));
        }

        if ($args['type'] == 'Wallet Deposit') {
            $stats->update([
                'wallet' => DB::raw('wallet + '.$args['amount']),
                'earnings' => DB::raw('earnings + '.$args['amount'])
            ]);
        }

        if (in_array($args['type'], ['Wallet Withdraw', 'Cashout', 'Scan And Pay'])) {
            if($stats->wallet < $args['amount']) {
                throw new CustomException(__('lang.insufficient_balance'));
            }

            $stats->update([
                'wallet' => DB::raw('wallet - '.$args['amount']),
                'earnings' => DB::raw('earnings - '.$args['amount'])
            ]);
        }
    }
}
