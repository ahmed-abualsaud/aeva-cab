<?php

namespace Aeva\Seats\Domain\Repository\Eloquent\Queries;

use App\Partner;
use App\Traits\Filterable;

use Aeva\Seats\Domain\Repository\Eloquent\BaseRepository;
use Aeva\Seats\Domain\Models\SeatsTripTerminalTransaction;
use Aeva\Seats\Domain\Exports\SeatsTripTerminalTransactionExport;
use Aeva\Seats\Domain\Repository\Queries\SeatsTripTerminalTransactionRepositoryInterface;

class SeatsTripTerminalTransactionRepository extends BaseRepository implements SeatsTripTerminalTransactionRepositoryInterface
{
    use Filterable;

    private $partner;

    public function __construct(SeatsTripTerminalTransaction $model,  Partner $partner)
    {
        parent::__construct($model);
        $this->partner = $partner;
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
            ROUND(SUM(amount), 2) as sum,
            COUNT(*) as count
        ');

        $transactions = $this->timeStatsScope($args, $transactions);

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

    public function export(Request $req) 
    {
        $filename = preg_replace('/-|:|\s+/', '_', now()).'_transactions.xlsx';
        $partner = $req->query('partner');
        $terminal = $req->query('terminal');
        $period = $req->query('period');
        $searchFor = $req->query('searchFor');
        $searchQuery = $req->query('searchQuery');

        return (new SeatsTripTerminalTransactionExport($partner, $terminal, $period, $searchFor, $searchQuery))
            ->download($filename);
    }

    protected function timeStatsScope($args, $transactions)
    {
        if (array_key_exists('scope', $args) && $args['scope'] === 'hours')
            return $transactions
                ->addSelect(\DB::raw('DATE_FORMAT(created_at, "%d %b %Y, %h %p") as time'));

        return $transactions
            ->addSelect(\DB::raw('DATE_FORMAT(created_at, "%d %b %Y") as time'));
    }
}
