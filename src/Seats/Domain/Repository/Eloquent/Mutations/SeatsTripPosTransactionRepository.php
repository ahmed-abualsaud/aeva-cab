<?php

namespace Qruz\Seats\Domain\Repository\Eloquent\Mutations;

use Illuminate\Support\Facades\DB;

use Qruz\Seats\Domain\Models\SeatsTripPosTransaction;
use Qruz\Seats\Domain\Repository\Eloquent\BaseRepository;


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
        $trxArr = [];
        $usersArr = [];

        foreach($args as $val) {
            $trxArr['ticket_id'] = $val['ticket_id'];
            $trxArr['partner_id'] = $val['partner_id'];
            $trxArr['driver_id'] = $val['driver_id'];
            $trxArr['vehicle_id'] = $val['vehicle_id'];
            $trxArr['serial'] = $val['serial'];
            $trxArr['amount'] = $val['amount'];
            $trxArr['created_at'] = $val['created_at'];

            if (array_key_exists('user_id', $val) && $val['user_id']) {
                $trxArr['user_id'] = $val['user_id'];
                $usersArr['user_id'] = $val['user_id'];
                $usersArr['amount'] = $val['amount'];
                $usersData[] = $usersArr;
            } else {
                $trxArr['user_id'] = null;
            }

            $trxData[] = $trxArr;

        }

        if ($usersArr) {
            $this->updateNfcBalance($usersData);
        }

        return $this->model->insert($trxData);
    }

    protected function updateNfcBalance(array $usersData)
    {
        try {
            
            $cases = []; $ids = []; $amount = [];

            foreach ($usersData as $value) {
                $id = (int) $value['user_id'];
                $cases[] = "WHEN {$id} then ?";
                $amount[] = $value['amount'];
                $ids[] = $id;
            }

            $ids = implode(',', $ids);
            $cases = implode(' ', $cases);
            $params = array_merge($amount);

            return DB::update("UPDATE `users` 
                SET `nfc_balance` = `nfc_balance` - CASE `id` {$cases} END
                WHERE `id` in ({$ids})", $params);
            
        } catch (\Exception $e) {
            //
        }
    }
}
