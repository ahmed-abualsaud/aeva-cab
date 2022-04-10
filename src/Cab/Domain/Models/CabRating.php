<?php

namespace Qruz\Cab\Domain\Models;

use App\Driver;

use Illuminate\Database\Eloquent\Model;

class CabRating extends Model
{
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
        return $query->select(
            'cab_requests.id'
            )
            ->join('cab_requests', 'cab_ratings.request_id', '=', 'cab_requests.id')
            ->where('cab_requests.user_id', $args['user_id'])
            ->whereNull('rating');
    }
}
