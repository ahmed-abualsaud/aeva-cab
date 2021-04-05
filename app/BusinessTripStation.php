<?php

namespace App;

use App\Scopes\SortByOrderScope;
use Illuminate\Database\Eloquent\Model;

class BusinessTripStation extends Model
{
    
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SortByOrderScope);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'business_trip_users', 'station_id', 'user_id')
            ->whereNotNull('business_trip_users.subscription_verified_at');
    }
}
