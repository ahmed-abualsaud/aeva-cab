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

    public function driver()
    {
        return $this->belongsTo(Driver::class)->select('id', 'name');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class)->select('id', 'license_plate');
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
            $events = $query->selectRaw('
                drivers.id AS driver_id, drivers.name AS driver_name, business_trip_events.*
            ')
            ->where('trip_id', $args['trip_id']);
        } else {
            $events = $query->selectRaw('
                business_trips.id AS trip_id, business_trips.name AS trip_name,
                business_trips.duration AS trip_duration, business_trips.distance AS trip_distance,
                drivers.id AS driver_id, drivers.name AS driver_name,
                business_trip_events.*
            ')
            ->join('business_trips', 'business_trips.id', '=', 'business_trip_events.trip_id');

            if (array_key_exists('partner_id', $args) && $args['partner_id']) {
                $events = $events->where('business_trips.partner_id', $args['partner_id']);
            }

            if (array_key_exists('type', $args) && $args['type']) {
                $events = $events->where('business_trips.type', $args['type']);
            }
        }

        return $query->join('drivers', 'drivers.id', '=', 'business_trip_events.driver_id')
            ->latest('business_trip_events.created_at');
    }

    public function scopeFilter($query, $args)
    {
        if (array_key_exists('period', $args) && $args['period']) {
            $query = $this->dateFilter($args['period'], $query, 'business_trip_events.created_at');
        }

        return $query;
    }
}
