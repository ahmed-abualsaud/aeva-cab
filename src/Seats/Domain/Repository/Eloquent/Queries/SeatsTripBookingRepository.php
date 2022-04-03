<?php

namespace Qruz\Seats\Domain\Repository\Eloquent\Queries;

use Qruz\Seats\Domain\Models\SeatsLineStation;
use Qruz\Seats\Domain\Repository\Eloquent\BaseRepository;
use Qruz\Seats\Domain\Repository\Queries\SeatsTripBookingRepositoryInterface;

class SeatsTripBookingRepository extends BaseRepository implements SeatsTripBookingRepositoryInterface
{
    public function __construct(SeatsLineStation $model)
    {
        parent::__construct($model);
    }

    public function pre(array $args)
    {
        $wallet = auth('user')->user()->wallet_balance;

        $pickup = $this->model->select('latitude', 'longitude')
            ->find($args['pickup_id']);

        $dropoff = $this->model->select('latitude', 'longitude')
            ->find($args['dropoff_id']);

        return [
            'wallet' => $wallet,
            'pickup' => $pickup,
            'dropoff' => $dropoff
        ];
    }
}
