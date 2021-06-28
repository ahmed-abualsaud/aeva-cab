<?php

namespace App\Exports;

use App\Partner;
use App\Traits\Filterable;
use App\SeatsTripTerminalTransaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SeatsTripTerminalTransactionExport implements FromQuery, WithHeadings
{
    use Exportable;
    use Filterable;

    public function __construct($partner = null, $terminal = null, $period = null)
    {
        $this->partner = $partner;
        $this->terminal = $terminal;
        $this->period = $period;
    }


    public function query()
    {
        $query = SeatsTripTerminalTransaction::query();

        if ($this->partner)
            $query = $query->where('partner_id', Partner::getPaymobID($this->partner));

        if ($this->terminal)
            $query = $query->where('terminal_id', $this->terminal);

        if ($this->period)
            $query = $this->dateFilter($this->period, $query, 'created_at');

        return $query
            ->select('created_at', 'trx_id', 'terminal_id', 'source', 'amount', 'status')
            ->latest();
    }

    public function headings() :array
    {
        return ['Transaction Date', 'Transaction ID', 'Terminal ID', 'Method', 'Amount', 'Status'];
    }
}
