<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace App\Exports;

use App\Driver;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Excel;

class DriverTransactionsExport implements FromQuery,WithHeadings,WithMapping,Responsable
{
    use Exportable;

    public $query;
    private $fileName = 'driver_transactions.csv';
    private $writerType = Excel::CSV;
    private $headers = [
        'Content-Type' => 'text/csv',
        'X-Vapor-Base64-Encode' => 'True'
    ];

    /**
     * @param Builder $query
     */
    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    /**
     * @return Builder
     */
    public function query()
    {
        return $this->query;
    }

    /**
     * @param $row
     * @return array
     */
    public function map($row): array
    {
        //attributes shown
        return [
            $row->id,
            $row->driver_id,
            $row->admin_id,
            $row->amount,
            $row->type,
            $row->notes,
            $row->created_at,
        ];
    }

    /**
     * @return string[]
     */
    public function headings(): array
    {
        //headers
        return [
            'id',
            'driver_id',
            'admin_id',
            'amount',
            'type',
            'notes',
            'created_at',
        ];
    }
}
