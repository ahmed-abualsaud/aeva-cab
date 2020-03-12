<?php

namespace App;

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
        return $this->hasMany(PartnerTripStation::class)->whereNotNull('accepted_at');
    }

    public function schedule()
    {
        return $this->hasOne(PartnerTripSchedule::class);
    }
}
