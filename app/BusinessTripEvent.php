<?php

namespace App;

use App\Traits\Searchable;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class BusinessTripEvent extends Model
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
        return $this->belongsTo(BusinessTrip::class);
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
            $events = $query->select('log_id', 'content', 'map_url', 'updated_at')
                ->where('trip_id', $args['trip_id']);
        } else {
            $events = $query->selectRaw('
                business_trips.id AS trip_id, business_trips.name AS trip_name,
                drivers.id AS driver_id, drivers.name AS driver_name,
                business_trip_events.log_id, business_trip_events.content, business_trip_events.map_url, business_trip_events.updated_at
            ')
            ->join('business_trips', 'business_trips.id', '=', 'business_trip_events.trip_id')
            ->join('drivers', 'drivers.id', '=', 'business_trips.driver_id');

            if (array_key_exists('partner_id', $args) && $args['partner_id']) {
                $events = $events->where('business_trips.partner_id', '=', $args['partner_id']);
            } else {
                $events = $events->join('partners', 'partners.id', '=', 'business_trips.partner_id')
                    ->addSelect('partners.id AS partner_id', 'partners.name AS partner_name');
            } 

            if (array_key_exists('belongs_to', $args) && $args['belongs_to']) {
                $events = $events->where('business_trips.belongs_to', $args['belongs_to']);
            }
        }

        return $events->latest('business_trip_events.updated_at');
    }

    public function scopeFilter($query, $args)
    {
        if (array_key_exists('period', $args) && $args['period']) {
            $query = $this->dateFilter($args['period'], $query, 'business_trip_events.updated_at');
        }

        return $query;
    }
}
