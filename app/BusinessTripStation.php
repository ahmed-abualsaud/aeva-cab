<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessTripStation extends Model
{
    
    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class, 'business_trip_users', 'station_id', 'user_id')
            ->whereNotNull('business_trip_users.subscription_verified_at');
    }
}
