<?php

namespace App;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class BusinessTripEvent extends Model
{
    use Filterable;
    
    protected $guarded = [];

    protected $primaryKey = 'log_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = [
        'content' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter($query, $args)
    {
        if (array_key_exists('trip_id', $args) && $args['trip_id']) {
            $events = $query->select('log_id', 'content', 'map_url')
                ->where('trip_id', $args['trip_id']);
        } else {
            $events = $query->selectRaw('
                business_trips.id AS trip_id, business_trips.name AS trip_name,
                business_trip_events.log_id, business_trip_events.content, business_trip_events.map_url
            ')
            ->join('business_trips', 'business_trips.id', '=', 'business_trip_events.trip_id');

            if (array_key_exists('partner_id', $args) && $args['partner_id']) {
                $events = $events->where('business_trips.partner_id', '=', $args['partner_id']);
            } else {
                $events = $events->join('partners', 'partners.id', '=', 'business_trips.partner_id')
                    ->addSelect('partners.id AS partner_id', 'partners.name AS partner_name');
            }   
        }

        if (array_key_exists('period', $args) && $args['period']) {
            $events = $this->dateFilter($args['period'], $events, 'business_trip_events.updated_at');
        }

        return $events->latest('business_trip_events.updated_at');
    }
}
