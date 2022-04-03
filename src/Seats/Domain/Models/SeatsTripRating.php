<?php

namespace Qruz\Seats\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class SeatsTripRating extends Model
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
        return $this->belongsTo(SeatsTrip::class);
    }

    public function scopeUnrated($query, $args) 
    {
        return $query->select(
            'seats_trip_ratings.id', 
            'seats_trips.name', 
            'seats_trip_ratings.trip_time as starts_at'
            )
            ->join('seats_trips', 'seats_trip_ratings.trip_id', '=', 'seats_trips.id')
            ->where('user_id', $args['user_id'])
            ->whereNull('rating');
    }
}
