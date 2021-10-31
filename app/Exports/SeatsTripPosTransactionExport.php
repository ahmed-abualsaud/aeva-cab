<?php

namespace App\Exports;

use App\Traits\Filterable;
use App\Traits\Searchable;
use App\SeatsTripPosTransaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SeatsTripPosTransactionExport implements FromQuery, WithHeadings
{
    use Exportable;
    use Filterable;
    use Searchable;

    public function __construct($partner = null, $period = null, $searchFor = null, $searchQuery = null)
    {
        $this->partner = $partner;
        $this->period = $period;
        $this->searchFor = $searchFor;
        $this->searchQuery = $searchQuery;
    }


    public function query()
    {
        $query = SeatsTripPosTransaction::query();

        if ($this->partner)
            $query = $query->where('seats_trip_pos_transactions.partner_id', $this->partner);

        if ($this->period)
            $query = $this->dateFilter($this->period, $query, 'seats_trip_pos_transactions.created_at');
        
        if ($this->searchFor && $this->searchQuery)
            $query = $this->search($this->searchFor, $this->searchQuery, $query);

        return $query
            ->leftJoin('vehicles', 'vehicles.id', '=', 'seats_trip_pos_transactions.vehicle_id')
            ->selectRaw('
                DATE_FORMAT(seats_trip_pos_transactions.created_at, "%d %b %Y, %h %i %s %p"),
                seats_trip_pos_transactions.id, 
                seats_trip_pos_transactions.serial, 
                vehicles.code, 
                seats_trip_pos_transactions.amount
            ')
            ->latest('seats_trip_pos_transactions.created_at');
    }

    public function headings() :array
    {
        return ['Transaction Date', 'Transaction ID', 'Vehicle Serial', 'Vehicle Code', 'Amount'];
    }
}
