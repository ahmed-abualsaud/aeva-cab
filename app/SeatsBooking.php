<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SeatsBooking extends Model
{

    protected $guarded = [];

    public function trip()
    {
        return $this->belongsTo(BusinessTrip::class);
    }

    public function user()
    {
        return $this->belongsTo(BusinessTrip::class);
    }

    public function pickup()
    {
        return $this->belongsTo(BusinessTripStation::class, 'pickup_id');
    }

    public function dropoff()
    {
        return $this->belongsTo(BusinessTripStation::class, 'dropoff_id');
    }

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class, 'promo_code_id');
    }

    public function scopeSearch($query, $args) 
    {
        
        if (array_key_exists('searchQuery', $args) && $args['searchQuery']) {
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);
        }

        return $query;
    }

    public function scopeWherePeriod($query, $args) 
    {
        
        if (array_key_exists('period', $args) && $args['period']) {
            $query = $this->dateFilter($args['period'], $query, 'created_at');
        }

        return $query->latest('created_at');
    }

    public function scopeWhereStatus($query, $args) 
    {
        if (array_key_exists('status', $args) && $args['status']) {
            $query->where('status', $args['status']);
        }

        return $query;
    }

    public function scopeWherePickupTime($query, $args) 
    {
        $now = date("Y-m-d H:i:s");

        switch($args['time']) {
            case 'past':
                return $query->where('pickup_time', '<', $now);
            default:
                return $query->where('pickup_time', '>=', $now);
        }

    }
}
