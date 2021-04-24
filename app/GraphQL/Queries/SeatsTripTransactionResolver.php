<?php

namespace App\GraphQL\Queries;

use App\Traits\Filterable;
use App\SeatsTripTransaction;

class SeatsTripTransactionResolver
{
    use Filterable;
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function stats($_, array $args)
    {
        $transactions = SeatsTripTransaction::query();

        $transactionGroup = SeatsTripTransaction::selectRaw('
            DATE_FORMAT(created_at, "%a, %b %d, %Y") as date,
            sum(amount) as sum
        ');

        if (array_key_exists('period', $args) && $args['period']) {
            $transactions = $this->dateFilter($args['period'], $transactions, 'created_at');
            $transactionGroup = $this->dateFilter($args['period'], $transactionGroup, 'created_at');
        }

        $transactionCount = $transactions->count();
        $transactionSum = $transactions->sum('amount');
        $transactionAvg = $transactions->avg('amount');
        $transactionGroup = $transactionGroup->groupBy('date')->get();

        $response = [
            "count" => $transactionCount,
            "sum" => $transactionSum,
            "avg" => $transactionAvg,
            "transactions" => $transactionGroup
        ];

        return $response;
    }
}
