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

class CabRequestsExport implements FromQuery,WithHeadings,WithMapping,Responsable
{
    use Exportable;

    public $query;
    private $fileName = 'cab_requests.csv';
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
            $row->user_id,
            $row->driver_id,
            $row->vehicle_id,
            $row->promo_code_id,
            $row->status,
            $row->history,
            $row->route_key,
            $row->map_url,
            $row->schedule_time,
            $row->next_free_time,
            $row->paid,
            $row->rated,
            $row->costs,
            $row->s_address,
            $row->s_lat,
            $row->s_lng,
            $row->d_address,
            $row->d_lat,
            $row->d_lng,
            $row->notes,
            $row->created_at,
            $row->updated_at,
            $row->deleted_at,
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
            'user_id',
            'driver_id',
            'vehicle_id',
            'promo_code_id',
            'status',
            'history',
            'route_key',
            'map_url',
            'schedule_time',
            'next_free_time',
            'paid',
            'rated',
            'costs',
            's_address',
            's_lat',
            's_lng',
            'd_address',
            'd_lat',
            'd_lng',
            'notes',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }
}