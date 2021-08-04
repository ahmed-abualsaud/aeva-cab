<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SeatsTripRating extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The user who created the request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The driver assigned to the request.
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * The trip assigned to the request.
     */
    public function trip()
    {
        return $this->belongsTo(SeatsTrip::class);
    }

    public function scopeUnrated($query, $args) 
    {
        return $query->join('seats_trips', function($join) use($args) {

            $join->on('seats_trip_ratings.trip_id', 'seats_trips.id')

            ->where('user_id', $args['user_id'])

            ->whereNull('rating');

        })->select('seats_trips.id', 'name', 'starts_at');
    }
}
