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

    public function scopeSearch($query, $args) 
    {
        if (array_key_exists('searchQuery', $args) && $args['searchQuery']) {
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);
        }

        return $query;
    }

    public function scopeTrip($query, $args)
    {
        if (array_key_exists('trip_id', $args) && $args['trip_id']) {
            $events = $query->select('log_id', 'content', 'map_url')
                ->where('trip_id', $args['trip_id']);
        } else {
            $events = $query->selectRaw('
                seats_trips.id AS trip_id, seats_trips.name AS trip_name,
                drivers.id AS driver_id, drivers.name AS driver_name,
                seats_trip_events.log_id, seats_trip_events.content, seats_trip_events.map_url
            ')
            ->join('seats_trips', 'seats_trips.id', '=', 'seats_trip_events.trip_id')
            ->join('drivers', 'drivers.id', '=', 'seats_trips.driver_id');

            if (array_key_exists('partner_id', $args) && $args['partner_id']) {
                $events = $events->where('seats_trips.partner_id', '=', $args['partner_id']);
            } else {
                $events = $events->join('partners', 'partners.id', '=', 'seats_trips.partner_id')
                    ->addSelect('partners.id AS partner_id', 'partners.name AS partner_name');
            } 
        }

        return $events->latest('seats_trip_events.created_at');
    }

    public function scopeFilter($query, $args)
    {
        if (array_key_exists('period', $args) && $args['period']) {
            $query = $this->dateFilter($args['period'], $query, 'seats_trip_events.created_at');
        }

        return $query;
    }
}

