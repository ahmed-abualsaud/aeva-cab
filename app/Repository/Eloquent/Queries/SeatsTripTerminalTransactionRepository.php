<?php

namespace App\Repository\Eloquent\Queries;

use App\Partner;
use App\Traits\Filterable;
use App\SeatsTripTerminalTransaction;
use App\Repository\Queries\SeatsTripTerminalTransactionRepositoryInterface;

class SeatsTripTerminalTransactionRepository extends BaseRepository implements SeatsTripTerminalTransactionRepositoryInterface
{
    use Filterable;

    private $partner;

    public function __construct(SeatsTripTerminalTransaction $model,  Partner $partner)
    {
        parent::__construct($model);
        $this->partner = $partner;
    }

    public function stats(array $args)
    {
        $transactions = $this->model->query();

        $transactionGroup = $this->model->selectRaw('
            DATE_FORMAT(created_at, "%a, %b %d, %Y") as date,
            ROUND(SUM(amount), 2) as sum
        ');

        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            $paymobID = $this->partner->getPaymobID($args['partner_id']);
            $transactions = $transactions->where('partner_id', $paymobID);
            $transactionGroup = $transactionGroup->where('partner_id', $paymobID);
        }

        if (array_key_exists('terminal_id', $args) && $args['terminal_id']) {
            $transactions = $transactions->where('terminal_id', $args['terminal_id']);
            $transactionGroup = $transactionGroup->where('terminal_id', $args['terminal_id']);
        }

        if (array_key_exists('period', $args) && $args['period']) {
            $transactions = $this->dateFilter($args['period'], $transactions, 'created_at');
            $transactionGroup = $this->dateFilter($args['period'], $transactionGroup, 'created_at');
        }

        $transactionCount = $transactions->count();
        $transactionSum = $transactions->sum('amount');
        $transactionGroup = $transactionGroup->groupBy('date')->get();

        $response = [
            'count' => $transactionCount,
            'sum' => round($transactionSum, 2),
            'transactions' => $transactionGroup
        ];

        return $response;
    }
}
