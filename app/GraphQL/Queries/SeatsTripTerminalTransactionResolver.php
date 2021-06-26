<?php

namespace App\GraphQL\Queries;

use App\Partner;
use App\Traits\Filterable;
use App\SeatsTripTerminalTransaction;

class SeatsTripTerminalTransactionResolver
{
    use Filterable;
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function stats($_, array $args)
    {
        $transactions = SeatsTripTerminalTransaction::query();

        $transactionGroup = SeatsTripTerminalTransaction::selectRaw('
            DATE_FORMAT(created_at, "%a, %b %d, %Y") as date,
            ROUND(SUM(amount), 2) as sum
        ');

        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            $paymobID = Partner::getPaymobID($args['partner_id']);
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
