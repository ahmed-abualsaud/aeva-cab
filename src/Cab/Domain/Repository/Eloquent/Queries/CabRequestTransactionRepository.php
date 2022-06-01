<?php

namespace Aeva\Cab\Domain\Repository\Eloquent\Queries;

use App\Traits\Filterable;

use Aeva\Cab\Domain\Models\CabRequestTransaction;

use Aeva\Cab\Domain\Repository\Eloquent\BaseRepository;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class CabRequestTransactionRepository extends BaseRepository
{
    use Filterable;
    
    public function __construct(CabRequestTransaction $model)
    {
        parent::__construct($model);
    }

    public function stats(array $args)
    {
        $transactions = $this->model->query();

        $transactionGroup = $this->model->selectRaw('
            DATE_FORMAT(created_at, "%d %b %Y") as x,
            ROUND(SUM(costs), 2) as y
        ');

        if (array_key_exists('period', $args) && $args['period']) {
            $transactions = $this->dateFilter($args['period'], $transactions, 'created_at');
            $transactionGroup = $this->dateFilter($args['period'], $transactionGroup, 'created_at');
        }

        $transactionCount = $transactions->count();
        $transactionSum = $transactions->sum('costs');
        $transactionGroup = $transactionGroup->groupBy('x')->get();

        $response = [
            'count' => $transactionCount,
            'sum' => round($transactionSum, 2),
            'transactions' => $transactionGroup
        ];

        return $response;
    }
}