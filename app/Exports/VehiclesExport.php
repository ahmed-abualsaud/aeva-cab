<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Excel;

class VehiclesExport implements FromQuery,WithHeadings,WithMapping,Responsable
{
    use Exportable;

    public $query;
    private $json_format;
    private $fileName = 'vehicles.csv';
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
        $this->query = $query->with(['make','type','drivers:id,phone,full_name','documents:id,name,url,notes,status,admin_id']);
        $this->json_format = JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|ENT_QUOTES;
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
        $drivers = collect($row->drivers->makeHidden(['pivot']))->toJson($this->json_format);
        $make = collect($row->make)->toJson($this->json_format);
        $type = collect($row->type)->toJson($this->json_format);
        $documents = collect($row->documents)->toJson($this->json_format);

        //attributes shown
        return [
            $row->id,
            $row->license_plate,
            $row->car_type_id,
            $row->car_model_id,
            $row->car_make_id,
            $row->license_expires_on,
            $row->color,
            $row->year,
            $row->seats,
            $row->photo,
            $row->partner_id,
            $row->terminal_id,
            $row->device_id,
            $row->code,
            $row->text,
            $row->created_at,
            $row->updated_at,
            $row->approved,
            $make,
            $type,
            $drivers,
            $documents,
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
            'license_plate',
            'car_type_id',
            'car_model_id',
            'car_make_id',
            'license_expires_on',
            'color',
            'year',
            'seats',
            'photo',
            'partner_id',
            'terminal_id',
            'device_id',
            'code',
            'text',
            'created_at',
            'updated_at',
            'approved',
            'make',
            'type',
            'drivers',
            'documents',
        ];
    }
}
