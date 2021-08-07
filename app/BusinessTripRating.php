<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessTripRating extends Model
{

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function trip()
    {
        return $this->belongsTo(BusinessTrip::class);
    }

    public function scopeUnrated($query, $args) 
    {
        return $query->select(
            'business_trip_ratings.id', 
            'business_trips.name', 
            'business_trip_ratings.trip_time as starts_at'
            )
            ->join('business_trips', 'business_trip_ratings.trip_id', '=', 'business_trips.id')
            ->where('user_id', $args['user_id'])
            ->whereNull('rating');
    }
}
