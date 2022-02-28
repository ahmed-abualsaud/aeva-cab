<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CabRating extends Model
{
    protected $guarded = [];

    public function scopeUnrated($query, $args) 
    {
        return $query->select(
            'cab_requests.id'
            )
            ->join('cab_requests', 'cab_ratings.request_id', '=', 'cab_requests.id')
            ->where('cab_requests.user_id', $args['user_id'])
            ->whereNull('rating');
    }
}
