<?php

namespace Aeva\Seats\Domain\Exports;

use App\Partner;
use App\Traits\Filterable;
use App\Traits\Searchable;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Aeva\Seats\Domain\Models\SeatsTripTerminalTransaction;

class SeatsTripTerminalTransactionExport implements FromQuery, WithHeadings
{
    use Exportable;
    use Filterable;
    use Searchable;

    public function __construct($partner = null, $terminal = null, $period = null, $searchFor = null, $searchQuery = null)
    {
        $this->partner = $partner;
        $this->terminal = $terminal;
        $this->period = $period;
        $this->searchFor = $searchFor;
        $this->searchQuery = $searchQuery;
    }


    public function query()
    {
        $query = SeatsTripTerminalTransaction::query();

        if ($this->partner)
            $query = $query->where('seats_trip_terminal_transactions.partner_id', Partner::getPaymobID($this->partner));

        if ($this->terminal)
            $query = $query->where('seats_trip_terminal_transactions.terminal_id', $this->terminal);

        if ($this->period)
            $query = $this->dateFilter($this->period, $query, 'seats_trip_terminal_transactions.created_at');
        
        if ($this->searchFor && $this->searchQuery)
            $query = $this->search($this->searchFor, $this->searchQuery, $query);

        return $query
            ->leftJoin('vehicles', 'vehicles.terminal_id', '=', 'seats_trip_terminal_transactions.terminal_id')
            ->select('seats_trip_terminal_transactions.created_at', 'seats_trip_terminal_transactions.trx_id', 'seats_trip_terminal_transactions.terminal_id', 'vehicles.license_plate', 'vehicles.code', 'seats_trip_terminal_transactions.source', 'seats_trip_terminal_transactions.amount', 'seats_trip_terminal_transactions.status')
            ->latest();
    }

    public function headings() :array
    {
        return ['Transaction Date', 'Transaction ID', 'Terminal ID', 'Vehicle Number', 'Vehicle Code', 'Method', 'Amount', 'Status'];
    }
}
