<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
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
        $driver = optional($row->driver);
        $admin = optional($row->admin);

        //attributes shown
        return [
            $row->id,
            $row->driver_id,
            $row->merchant_id,
            $row->merchant_name,
            $row->costs,
            $row->payment_method,
            $row->uuid,
            $row->created_at,
            $row->updated_at,
            $row->deleted_at,
            //driver attributes
            $driver->id,
            $driver->first_name,
            $driver->last_name,
            $driver->full_name,
            $driver->phone,
            $driver->email,
            $driver->license_expires_on,
            $driver->city,
            $driver->vehicle,
            $driver->fleet_id,
            $driver->partner_id,
            $driver->car_type_id,
            $driver->latitude,
            $driver->longitude,
            $driver->status,
            $driver->cab_status,
            $driver->phone_verified_at,
            $driver->provider,
            $driver->provider_id,
            $driver->device_id,
            $driver->code,
            $driver->created_at,
            $driver->updated_at,
            $driver->referrer_id,
            $driver->ref_code,
            $driver->secondary_phone,
            $driver->approved,
            $driver->title,
            $driver->natiaonal_id,
            //admin attributes id,full_name,phone,email
             $admin->id,
             $admin->full_name,
             $admin->phone,
             $admin->email,
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
            'merchant_id',
            'merchant_name',
            'costs',
            'payment_method',
            'uuid',
            'created_at',
            'updated_at',
            'deleted_at',
            //driver attributes
            'driver:id',
            'driver:first_name',
            'driver:last_name',
            'driver:full_name',
            'driver:phone',
            'driver:email',
            'driver:license_expires_on',
            'driver:city',
            'driver:vehicle',
            'driver:fleet_id',
            'driver:partner_id',
            'driver:car_type_id',
            'driver:latitude',
            'driver:longitude',
            'driver:status',
            'driver:cab_status',
            'driver:phone_verified_at',
            'driver:provider',
            'driver:provider_id',
            'driver:device_id',
            'driver:code',
            'driver:created_at',
            'driver:updated_at',
            'driver:referrer_id',
            'driver:ref_code',
            'driver:secondary_phone',
            'driver:approved',
            'driver:title',
            'driver:national_id',
            //admin attributes id,full_name,phone,email
            'admin:id',
            'admin:full_name',
            'admin:phone',
            'admin:email',
        ];
    }
}
