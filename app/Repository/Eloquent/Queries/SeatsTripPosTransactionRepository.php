<?php

namespace App\Repository\Eloquent\Queries;

use App\Traits\Filterable;
use App\SeatsTripPosTransaction;
use App\Repository\Queries\SeatsTripPosTransactionRepositoryInterface;
use App\Repository\Eloquent\BaseRepository;

class SeatsTripPosTransactionRepository extends BaseRepository implements SeatsTripPosTransactionRepositoryInterface
{
    use Filterable;


    public function __construct(SeatsTripPosTransaction $model)
    {
        parent::__construct($model);
    }

    public function vehiclesStats(array $args)
    {
        $vehicles = $this->model->selectRaw('
            license_plate, code, vehicles.terminal_id,
            ROUND(SUM(amount), 2) as sum,
            COUNT(seats_trip_pos_transactions.id) as count
        ')
        ->join('vehicles', 'vehicles.id', '=', 'seats_trip_pos_transactions.vehicle_id');

        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            $vehicles = $vehicles->where('seats_trip_pos_transactions.partner_id', $args['partner_id']);
        }

        if (array_key_exists('period', $args) && $args['period']) {
            $vehicles = $this->dateFilter($args['period'], $vehicles, 'seats_trip_pos_transactions.created_at');
        }

        return $vehicles->groupBy('license_plate')
          ->orderBy('sum', 'desc')
          ->get();
    }

    public function timeStats(array $args)
    {
        $transactions = $this->model->selectRaw('
            ROUND(SUM(amount), 2) as sum,
            COUNT(*) as count
        ');

        $transactions = $this->timeStatsScope($args, $transactions);

        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            $transactions = $transactions->where('partner_id', $args['partner_id']);
        }

        if (array_key_exists('vehicle_id', $args) && $args['vehicle_id']) {
          $transactions = $transactions->where('vehicle_id', $args['vehicle_id']);
        }

        if (array_key_exists('period', $args) && $args['period']) {
            $transactions = $this->dateFilter($args['period'], $transactions, 'created_at');
        }

        return $transactions->groupBy('time')
            ->orderBy('sum', 'desc')
            ->get();
    }

    protected function timeStatsScope($args, $transactions)
    {
        if (array_key_exists('scope', $args) && $args['scope'] === 'hours')
            return $transactions
                ->addSelect(\DB::raw('DATE_FORMAT(created_at, "%d %b %Y, %h %p") as time'));

        return $transactions
            ->addSelect(\DB::raw('DATE_FORMAT(created_at, "%d %b %Y") as time'));
    }

    public function vehicleMaxSerial(array $args)
    {
        return $this->model
            ->where('vehicle_id', $args['vehicle_id'])
            ->max('serial');
    }
}
