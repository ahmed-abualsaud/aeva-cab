<?php

namespace App;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class BusinessTrip extends Model
{
    use Searchable;
    
    protected $guarded = [];

    protected $casts = [
        'days' => 'array'
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function stations() 
    {        
        $stations = $this->hasMany(BusinessTripStation::class, 'trip_id');

        if (auth('user')->user()) {
            $stations->selectRaw('
                business_trip_stations.*, 
                station.station_id AS is_my_station, 
                destination.destination_id AS is_my_destination
            ')
            ->leftJoin('business_trip_users as station', function ($join) {
                $join->on('station.station_id', '=', 'business_trip_stations.id')
                    ->where('station.user_id', auth('user')->user()->id);
            })
            ->leftJoin('business_trip_users as destination', function ($join) {
                $join->on('destination.destination_id', '=', 'business_trip_stations.id')
                    ->where('destination.user_id', auth('user')->user()->id);
            });
        }

        return $stations->whereNotNull('accepted_at');
    }

    public function userStation() 
    {        
        return $this->hasOne(BusinessTripStation::class, 'trip_id')
            ->join('business_trip_users', 'business_trip_users.station_id', '=', 'business_trip_stations.id')
            ->where('business_trip_users.user_id', auth('user')->user()->id)
            ->selectRaw('business_trip_stations.*');

    }

    public function userDestination() 
    {        
        return $this->hasOne(BusinessTripStation::class, 'trip_id')
            ->join('business_trip_users', 'business_trip_users.destination_id', '=', 'business_trip_stations.id')
            ->where('business_trip_users.user_id', auth('user')->user()->id)
            ->selectRaw('business_trip_stations.*');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'business_trip_users', 'trip_id', 'user_id')
            ->whereNotNull('business_trip_users.subscription_verified_at');
    }

    public function scopeAvailableLines($query, $args) 
    {
        return $query->where('id', '<>', $args['trip_id']);
    }

    public function scopeLive($query, $args) 
    {
        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            $query->where('partner_id', $args['partner_id']);
        }

        return $query->where('status', true);
    }

    public function scopePartner($query, $args) 
    {
        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            $query->where('partner_id', $args['partner_id']);
        }
 
        return $query->orderBy('created_at', 'DESC');
    }

    public function scopeSearch($query, $args) 
    {
        
        if (array_key_exists('searchQuery', $args) && $args['searchQuery']) {
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);
        }

        return $query;
    }
    
}
