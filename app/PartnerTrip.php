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
        return $this->hasMany(PartnerTripStation::class, 'trip_id')
            ->whereNotNull('accepted_at');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'partner_trip_users', 'trip_id', 'user_id')
            ->whereNotNull('partner_trip_users.subscription_verified_at');
    } 

    public function schedule()
    {
        return $this->hasOne(PartnerTripSchedule::class, 'trip_id');
    }

    public function scopePredefinedStations($query, $args) 
    {
        return $query->where('id', '<>', $args['trip_id']);
    }
    
}
