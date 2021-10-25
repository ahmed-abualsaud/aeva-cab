<?php

namespace App\Repository\Eloquent\Mutations;

use App\SeatsTripAppTransaction;
use App\Repository\Eloquent\BaseRepository;
use App\SeatsTripBooking;

class SeatsTripAppTransactionRepository extends BaseRepository
{
    public function __construct(SeatsTripAppTransaction $model)
    {
        parent::__construct($model);
    }

    public function create(array $args)
    {
        $input = collect($args)->except(['directive'])->toArray();
        $input['trip_time'] = SeatsTripBooking::find($args['booking_id'])->trip_time;
        return $this->model->create($input);
    }

    public function destroy(array $args)
    {
        return $this->model->whereIn('id', $args['id'])->delete();
    }
}
