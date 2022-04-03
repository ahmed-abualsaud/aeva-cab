<?php

namespace Qruz\Seats\Domain\Repository\Eloquent\Mutations;

use Qruz\Seats\Domain\Models\SeatsTripBooking;
use Qruz\Seats\Domain\Models\SeatsTripAppTransaction;
use Qruz\Seats\Domain\Repository\Eloquent\BaseRepository;

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
