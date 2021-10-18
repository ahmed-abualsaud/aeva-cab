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
        $input = [
            'partner_id' => $args['partner_id'],
            'driver_id' => $args['driver_id'],
            'vehicle_id' => $args['vehicle_id'],
            'amount' => $args['amount'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        if ($args['tickets'] > 1) {
            for ($i = 0; $i < $args['tickets']; $i++) {
                $data[] = $input;
            }
            $this->model->insert($data);
            return $this->model
                ->where('vehicle_id', $input['vehicle_id'])
                ->limit($args['tickets'])
                ->latest()
                ->get();
        } else {
            $created = $this->model->create($input);
            return array($created);
        }
    }
}
