<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    protected $guarded = [];

    public $table = 'business_trip_followers';

    public function trip()
    {
        return $this->belongsTo(BusinessTrip::class);
    }

    public function scopeTrip($query, $args)
    {
        return $query->join('business_trips', 'business_trip_followers.trip_id', '=', 'business_trips.id')
            ->where('follower_id', $args['follower_id']);
    }

    public function scopeFollower($query, $args)
    {
        return $query->select('business_trip_followers.id', 'name', 'avatar', 'trip_id')
            ->join('users', 'business_trip_followers.user_id', '=', 'users.id')
            ->where('user_id', $args['user_id']);
    }
}
