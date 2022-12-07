<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Excel;

class CabRequestTransactionsExport implements FromQuery,WithHeadings,WithMapping,Responsable
{
    use Exportable;

    public $query;
    private $fileName = 'cab_request_transactions.csv';
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
        $user = optional($row->user);
        $driver = optional($row->driver);
        //attributes shown
        return [
            $row->id,
            $row->user_id,
            $row->driver_id,
            $row->request_id,
            $row->merchant_id,
            $row->merchant_name,
            $row->costs,
            $row->payment_method,
            $row->reference_number,
            $row->uuid,
            $row->created_at,
            $row->updated_at,
            $row->deleted_at,
            //user attributes
            $user->id,
            $user->first_name,
            $user->last_name,
            $user->full_name,
            $user->phone,
            $user->email,
            $user->is_active,
            $user->verification,
            $user->id_number,
            $user->id_expired_at,
            $user->verified_national_id,
            $user->verified_selfie_your_id,
            $user->kyc_rejection,
            $user->kyc_change_notified,
            $user->wallet,
            $user->debt,
            $user->referral_code,
            $user->suspended,
            $user->suspension_reason,
            $user->suspension_till,
            $user->deleted_at,
            $user->created_at,
            $user->updated_at,
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
            'request_id',
            'merchant_id',
            'merchant_name',
            'costs',
            'payment_method',
            'reference_number',
            'uuid',
            'created_at',
            'updated_at',
            'deleted_at',
            //user attributes
            'user:id',
            'user:first_name',
            'user:last_name',
            'user:full_name',
            'user:phone',
            'user:email',
            'user:is_active',
            'user:verification',
            'user:id_number',
            'user:id_expired_at',
            'user:verified_national_id',
            'user:verified_selfie_your_id',
            'user:kyc_rejection',
            'user:kyc_change_notified',
            'user:wallet',
            'user:debt',
            'user:referral_code',
            'user:suspended',
            'user:suspension_reason',
            'user:suspension_till',
            'user:deleted_at',
            'user:created_at',
            'user:updated_at',
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
        ];
    }
}
