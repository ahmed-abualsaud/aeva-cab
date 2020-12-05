<?php

namespace App;

use App\Traits\Searchable;
use App\BusinessTripSchedule;
use Illuminate\Database\Eloquent\Model;

class BusinessTrip extends Model
{
    use Searchable;
    
    protected $guarded = [];

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
            $stations->leftJoin('business_trip_users', function ($join) {
                $join->on('business_trip_users.station_id', '=', 'business_trip_stations.id')
                    ->where('business_trip_users.user_id', auth('user')->user()->id);
            })
            ->selectRaw('business_trip_stations.*, business_trip_users.station_id AS is_my_station');
        }

        $stations->whereNotNull('accepted_at')
            ->orderBy('distance', 'ASC');

        return $stations;
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'business_trip_users', 'trip_id', 'user_id')
            ->whereNotNull('business_trip_users.subscription_verified_at');
    } 

    public function schedule()
    {
        return $this->hasOne(BusinessTripSchedule::class, 'trip_id');
    }

    public function scopeAvailableLines($query, $args) 
    {
        return $query->where('id', '<>', $args['trip_id']);
    }

    public function scopeLive($query) 
    {
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
