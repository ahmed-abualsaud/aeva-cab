<?php

namespace App\Repository\Eloquent\Mutations;

use App\SeatsTripPosTransaction;
use App\Repository\Eloquent\BaseRepository;


class SeatsTripPosTransactionRepository extends BaseRepository
{
    public function __construct(SeatsTripPosTransaction $model)
    {
        parent::__construct($model);
    }

    public function create(array $args)
    {
        $input = collect($args)->except(['directive'])->toArray();
        return $this->model->create($input);
    }
}
