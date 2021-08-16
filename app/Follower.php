<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    protected $guarded = [];

    public $table = 'business_trip_followers';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function trip()
    {
        return $this->belongsTo(BusinessTrip::class);
    }

    public function scopeTrip($query, $args)
    {
        return $query->join('business_trips', 'business_trip_followers.trip_id', '=', 'business_trips.id')
            ->where('follower_id', $args['follower_id']);
    }
}
