<?php

namespace App;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class CabRequest extends Model
{
    use Filterable;
    use Searchable;

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

    public function scopeLive($query)
    {
        return $query->where('status', 'STARTED');
    }

    public function scopeTime($query, $args)
    {
        if (array_key_exists('time', $args) && $args['time']) {
            $now = date('Y-m-d H:i:s');

            switch($args['time']) {
                case 'PAST':
                    $query = $query->where('status', '<>', 'SCHEDULED');
                break;
                default:
                    $query = $query->where('status', 'SCHEDULED');
            }

            return $query->latest('schedule_time');
        }        
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

        return $query;
    }

    public function scopeFilter($query, $args) 
    {
        
        if (array_key_exists('status', $args) && $args['status']) {
            $query = $query->where('status', $args['status']);
        }

        if (array_key_exists('period', $args) && $args['period']) {
            $query = $this->dateFilter($args['period'], $query, 'created_at');
        }

        return $query->latest();
    }
}
