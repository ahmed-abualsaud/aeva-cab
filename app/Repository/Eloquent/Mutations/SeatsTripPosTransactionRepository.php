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
        $serial = $this->model
            ->where('vehicle_id', $args['vehicle_id'])
            ->max('serial') + 1;

        $input = [
            'partner_id' => $args['partner_id'],
            'driver_id' => $args['driver_id'],
            'vehicle_id' => $args['vehicle_id'],
            'amount' => $args['amount'],
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($args['tickets'] > 1) {
            for ($i = 0; $i < $args['tickets']; $i++) {
                $input['serial'] = $serial;
                $data[] = $input;
                $serial++;
            }
            $this->model->insert($data);
            return $this->model
                ->where('vehicle_id', $input['vehicle_id'])
                ->limit($args['tickets'])
                ->latest('id')
                ->get();
        } else {
            $input['serial'] = $serial;
            $created = $this->model->create($input);
            return array($created);
        }
    }

    public function bulkCreate(array $args)
    {
        $arr = [];
        foreach($args as $val) {
            $arr['ticket_id'] = $val['ticket_id'];
            $arr['partner_id'] = $val['partner_id'];
            $arr['driver_id'] = $val['driver_id'];
            $arr['vehicle_id'] = $val['vehicle_id'];
            $arr['serial'] = $val['serial'];
            $arr['amount'] = $val['amount'];
            $arr['created_at'] = $val['created_at'];
            $data[] = $arr;
        }

        return $this->model->insert($data);
    }
}
