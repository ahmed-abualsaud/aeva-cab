<?php

namespace App;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class SeatsTripTransaction extends Model
{
    use Searchable, Filterable;

    protected $guarded = [];

    public function trip()
    {
        return $this->belongsTo(SeatsTrip::class)
            ->select('id', 'name');
    }

    public function user()
    {
        return $this->belongsTo(User::class)
            ->select('id', 'name');
    }

    public function scopeSearch($query, $args) 
    {
        if (array_key_exists('searchQuery', $args) && $args['searchQuery']) {
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);
        }

        return $query;
    }

    public function scopeForPeriod($query, $args)
    {
        if (array_key_exists('period', $args) && $args['period']) {
            $query = $this->dateFilter($args['period'], $query, 'created_at');
        }

        return $query->latest();
    }

    public function scopeForPartner($query, $args) 
    {
        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            return $query->whereHas('trip', function($query) use ($args) {
                $query->where('partner_id', $args['partner_id']);
            });
        }
 
        return $query;
    }

    public function scopeForTrip($query, $args) 
    {
        if (array_key_exists('trip_id', $args) && $args['trip_id']) {
            return $query->where('trip_id', $args['trip_id']);
        }
 
        return $query;
    }
}
