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

class DriversExport implements FromQuery,WithHeadings,WithMapping,Responsable
{
    use Exportable;

    public $query;
    private $fileName = 'drivers.csv';
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
            $row->first_name,
            $row->last_name,
            $row->full_name,
            $row->email,
//            $row->password,
            $row->phone,
            $row->license_expires_on,
            $row->avatar,
            $row->city,
            $row->vehicle,
            $row->fleet_id,
            $row->partner_id ,
            $row->car_type_id ,
            $row->latitude ,
            $row->longitude ,
            $row->status ,
            $row->cab_status ,
            $row->phone_verified_at ,
            $row->provider ,
            $row->provider_id ,
            $row->device_id ,
            $row->code ,
            $row->created_at,
            $row->updated_at,
            $row->deleted_at,
            $row->referrer_id,
            $row->ref_code,
            $row->secondary_phone,
            $row->title,
            $row->approved,
            $row->natiaonal_id,
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
            'first_name',
            'last_name',
            'full_name',
            'email',
//            $row->password,
            'phone',
            'license_expires_on',
            'avatar',
            'city',
            'vehicle',
            'fleet_id',
            'partner_id' ,
            'car_type_id' ,
            'latitude' ,
            'longitude' ,
            'status' ,
            'cab_status' ,
            'phone_verified_at' ,
            'provider' ,
            'provider_id' ,
            'device_id' ,
            'code' ,
            'created_at',
            'updated_at',
            'deleted_at',
            'referrer_id',
            'ref_code',
            'secondary_phone',
            'title',
            'approved',
            'natiaonal_id',
        ];
    }
}