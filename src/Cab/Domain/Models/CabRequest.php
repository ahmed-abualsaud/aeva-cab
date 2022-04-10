<?php

namespace Qruz\Cab\Domain\Models;

use App\User;
use App\Driver;
use App\Vehicle;

use App\Traits\Filterable;
use App\Traits\Searchable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CabRequest extends Model
{
    use Filterable;
    use Searchable;
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'history' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function rating()
    {
        return $this->hasOne(CabRating::class, 'request_id');
    }

    public function transaction()
    {
        return $this->hasOne(CabRequestTransaction::class, 'request_id');
    }

    public function scopeLive($query)
    {
        return $query->whereIn('status', ['ACCEPTED', 'STARTED']);
    }

    public function scopeTime($query, $args)
    {
        if (array_key_exists('time', $args) && $args['time']) {
            switch($args['time']) {
                case 'PAST':
                    $query = $query->where('status', '<>', 'SCHEDULED');
                break;
                default:
                    $query = $query->where('status', 'SCHEDULED');
            }
        }
        
        return $query->latest();
    }

    public function scopeWherePending($query, $user_id)
    {
        return $query->where('user_id', $user_id)
            ->whereNotIn('status' , ['SCHEDULED', 'CANCELLED', 'COMPLETED']);
    }

    public function scopeWhereScheduled($query, $user_id)
    {
        return $query->where('user_id', $user_id)
            ->where('status', 'SCHEDULED');
    }

    public function scopeSearch($query, $args) 
    {
        
        if (array_key_exists('searchQuery', $args) && $args['searchQuery']) {
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);
        }

        return $query->latest();
    }

    public function scopeFilter($query, $args) 
    {
        if (array_key_exists('driver_id', $args) && $args['driver_id']) {
            $query = $query->where('driver_id', $args['driver_id']);
        }

        if (array_key_exists('user_id', $args) && $args['user_id']) {
            $query = $query->where('user_id', $args['user_id']);
        }
        
        if (array_key_exists('status', $args) && $args['status']) {
            $query = $query->where('status', $args['status']);
        }

        if (array_key_exists('period', $args) && $args['period']) {
            $query = $this->dateFilter($args['period'], $query, 'created_at');
        }

        return $query->latest();
    }

    public function scopePending($query, $args)
    {
        return $query->where($args['issuer_type'].'_id', $args['issuer_id'])
            ->whereNotIn('status' , ['SCHEDULED', 'CANCELLED', 'COMPLETED']);
    }
}
