<?php

namespace Qruz\Cab\Domain\Repository\Eloquent\Queries;

use Qruz\Cab\Domain\Models\CabRequestTransaction;

use Qruz\Cab\Domain\Repository\Eloquent\BaseRepository;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class CabRequestTransactionRepository extends BaseRepository
{
    public function __construct(CabRequestTransaction $model)
    {
        parent::__construct($model);
    }

    public function report($args) 
    {
        $report = $this->model->selectRaw('
            ROUND(SUM(amount), 2) as sum,
            COUNT(id) as count
        ')
        ->where('created_at', '>=', $args['date_from'])
        ->where('created_at', '<=', $args['date_to']);
        return $report->orderBy('sum', 'desc')->get();
    }
}