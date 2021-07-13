<?php

namespace App\Repository\Eloquent\Mutations;

use App\SeatsTripAppTransaction;
use App\Repository\Eloquent\BaseRepository;


class SeatsTripAppTransactionRepository extends BaseRepository
{
    public function __construct(SeatsTripAppTransaction $model)
    {
        parent::__construct($model);
    }

    public function create(array $args)
    {
        $input = collect($args)->except(['directive'])->toArray();
        return $this->model->create($input);
    }

    public function destroy(array $args)
    {
        return $this->model->whereIn('id', $args['id'])->delete();
    }
}
