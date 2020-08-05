<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessTripStation extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'business_trip_users', 'station_id', 'user_id')
            ->whereNotNull('business_trip_users.subscription_verified_at');
    }
}
