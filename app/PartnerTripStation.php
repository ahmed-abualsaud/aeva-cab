<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerTripStation extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'partner_trip_station_users', 'station_id', 'user_id');
    }
}
