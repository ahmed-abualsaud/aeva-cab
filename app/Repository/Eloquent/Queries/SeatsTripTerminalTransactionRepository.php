<?php

namespace App\Repository\Eloquent\Queries;

use App\Partner;
use App\Traits\Filterable;
use App\SeatsTripTerminalTransaction;
use App\Repository\Queries\SeatsTripTerminalTransactionRepositoryInterface;
use App\Repository\Eloquent\BaseRepository;

class SeatsTripTerminalTransactionRepository extends BaseRepository implements SeatsTripTerminalTransactionRepositoryInterface
{
    use Filterable;

    private $partner;

    public function __construct(SeatsTripTerminalTransaction $model,  Partner $partner)
    {
        parent::__construct($model);
        $this->partner = $partner;
    }

    public function stats(array $args)
    {
        $transactions = $this->model->query();

        $transactionsGroup = $this->model->selectRaw('
            DATE_FORMAT(created_at, "%a, %b %d, %Y") as date,
            ROUND(SUM(amount), 2) as sum
        ');

        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            $paymobID = $this->partner->getPaymobID($args['partner_id']);
            $transactions = $transactions->where('partner_id', $paymobID);
            $transactionsGroup = $transactionsGroup->where('partner_id', $paymobID);
        }

        if (array_key_exists('terminal_id', $args) && $args['terminal_id']) {
            $transactions = $transactions->where('terminal_id', $args['terminal_id']);
            $transactionsGroup = $transactionsGroup->where('terminal_id', $args['terminal_id']);
        }

        if (array_key_exists('period', $args) && $args['period']) {
            $transactions = $this->dateFilter($args['period'], $transactions, 'created_at');
            $transactionsGroup = $this->dateFilter($args['period'], $transactionsGroup, 'created_at');
        }

        $transactionCount = $transactions->count();
        $transactionSum = $transactions->sum('amount');
        $transactionsGroup = $transactionsGroup->groupBy('date')->get();

        $response = [
            'count' => $transactionCount,
            'sum' => round($transactionSum, 2),
            'transactions' => $transactionsGroup
        ];

        return $response;
    }

    public function vehiclesStats(array $args)
    {
        $vehicles = $this->model->selectRaw('
            license_plate, code, vehicles.terminal_id,
            ROUND(SUM(amount), 2) as sum,
            COUNT(seats_trip_terminal_transactions.id) as count
        ')
        ->join('vehicles', 'vehicles.terminal_id', '=', 'seats_trip_terminal_transactions.terminal_id');

        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            $paymobID = $this->partner->getPaymobID($args['partner_id']);
            $vehicles = $vehicles->where('seats_trip_terminal_transactions.partner_id', $paymobID);
        }

        if (array_key_exists('period', $args) && $args['period']) {
            $vehicles = $this->dateFilter($args['period'], $vehicles, 'seats_trip_terminal_transactions.created_at');
        }

        return $vehicles->groupBy('license_plate')
            ->orderBy('sum', 'desc')
            ->get();
    }

    public function timeStats(array $args)
    {

        $transactions = $this->model->selectRaw('
            DATE_FORMAT(created_at, "%d %b %Y, %h %p") as time,
            ROUND(SUM(amount), 2) as sum,
            COUNT(*) as count
        ');

        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            $paymobID = $this->partner->getPaymobID($args['partner_id']);
            $transactions = $transactions->where('partner_id', $paymobID);
        }

        if (array_key_exists('terminal_id', $args) && $args['terminal_id']) {
            $transactions = $transactions->where('terminal_id', $args['terminal_id']);
        }

        if (array_key_exists('period', $args) && $args['period']) {
            $transactions = $this->dateFilter($args['period'], $transactions, 'created_at');
        }

        return $transactions->groupBy('time')
            ->orderBy('sum', 'desc')
            ->get();
    }
}
