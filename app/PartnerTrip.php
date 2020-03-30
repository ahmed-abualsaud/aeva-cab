<?php

namespace App;

use App\PartnerTripSchedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerTrip extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function stations() 
    {
        $today = strtolower(date('l'));
        return $this->hasMany(PartnerTripStation::class, 'trip_id')
            ->select('*')
            ->addSelect(['shouldBeThereAt' => PartnerTripSchedule::selectRaw("UNIX_TIMESTAMP(ADDTIME($today, partner_trip_stations.time_from_start))*1000")
                ->whereColumn('partner_trip_stations.trip_id', 'partner_trip_schedules.trip_id')
            ])
            ->whereNotNull('accepted_at');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'partner_trip_users', 'trip_id', 'user_id');
    }

    public function schedule()
    {
        return $this->hasOne(PartnerTripSchedule::class, 'trip_id');
    }
}
