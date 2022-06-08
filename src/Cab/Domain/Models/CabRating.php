<?php

namespace Aeva\Cab\Domain\Models;

use App\User;
use App\Driver;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CabRating extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function user()
    {
        return $this->setConnection('mysql2')->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->setConnection('mysql')->belongsTo(Driver::class);
    }

    public function request()
    {
        return $this->setConnection('mysql')->belongsTo(CabRequest::class);
    }

    public function scopeUnrated($query, $args) 
    {
        if (array_key_exists('user_id', $args) && $args['user_id'] != null) 
        {
            return $query->join('cab_requests', 'cab_ratings.request_id', '=', 'cab_requests.id')
                ->where('cab_requests.user_id', $args['user_id'])
                ->whereNull('driver_rating');
        }

        if (array_key_exists('driver_id', $args) && $args['driver_id'] != null) 
        {
            return $query->join('cab_requests', 'cab_ratings.request_id', '=', 'cab_requests.id')
                ->where('cab_requests.driver_id', $args['driver_id'])
                ->whereNull('driver_rating');
        }
    }

    public function scopeLatest($query, $args) 
    {
        return $query->latest();
    }
}
