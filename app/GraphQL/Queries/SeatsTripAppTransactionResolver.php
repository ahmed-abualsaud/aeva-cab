<?php

namespace App\GraphQL\Queries;

use App\Traits\Filterable;
use App\SeatsTripAppTransaction;

class SeatsTripAppTransactionResolver
{
    use Filterable;
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function stats($_, array $args)
    {
        $transactions = SeatsTripAppTransaction::query();

        $transactionGroup = SeatsTripAppTransaction::selectRaw('
            DATE_FORMAT(created_at, "%a, %b %d, %Y") as date,
            ROUND(SUM(amount), 2) as sum
        ');

        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            $transactions = $transactions->whereHas('trip', function($query) use ($args) {
                $query->where('partner_id', $args['partner_id']);
            });
            $transactionGroup = $transactionGroup->whereHas('trip', function($query) use ($args) {
                $query->where('partner_id', $args['partner_id']);
            });
        }

        if (array_key_exists('period', $args) && $args['period']) {
            $transactions = $this->dateFilter($args['period'], $transactions, 'created_at');
            $transactionGroup = $this->dateFilter($args['period'], $transactionGroup, 'created_at');
        }

        $transactionCount = $transactions->count();
        $transactionSum = $transactions->sum('amount');
        $transactionAvg = $transactions->avg('amount');
        $transactionGroup = $transactionGroup->groupBy('date')->get();

        $response = [
            'count' => $transactionCount,
            'sum' => round($transactionSum, 2),
            'avg' => round($transactionAvg, 2),
            'transactions' => $transactionGroup
        ];

        return $response;
    }
}
