<?php

namespace App;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class SeatsTripEvent extends Model
{
    use Searchable, Filterable;
    
    protected $guarded = [];

    protected $primaryKey = 'log_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = [
        'content' => 'json',
    ];

    public function trip()
    {
        return $this->belongsTo(SeatsTrip::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class)->select('id', 'name');
    }

    public function scopeSearch($query, $args) 
    {
        if (array_key_exists('searchQuery', $args) && $args['searchQuery'])
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);

        return $query->latest();
    }

    public function scopeTrip($query, $args)
    {
        if (array_key_exists('trip_id', $args) && $args['trip_id'])
            return $query->where('trip_id', $args['trip_id']);

        return $query;
    }

    public function scopePartner($query, $args)
    {
        if (array_key_exists('partner_id', $args) && $args['partner_id'])
            return $query->whereHas('trip', function($query) use ($args) {
                $query->where('partner_id', $args['partner_id']);
            });

        return $query;
    }

    public function scopeFilter($query, $args)
    {
        if (array_key_exists('period', $args) && $args['period']) {
            $query = $this->dateFilter($args['period'], $query, 'seats_trip_events.created_at');
        }

        return $query;
    }
}

