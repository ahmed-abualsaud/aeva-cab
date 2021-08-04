<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessTripRating extends Model
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
        return $this->belongsTo(BusinessTrip::class);
    }

    public function scopeUnrated($query, $args) 
    {
        $query->join('business_trips', function($join) use($args) {

            $join->on('business_trip_ratings.trip_id', 'business_trips.id')

            ->where('user_id', $args['user_id'])

            ->whereNull('rating');

        })->select('business_trips.id', 'name', 'starts_at');
    }
}
