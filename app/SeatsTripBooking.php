<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SeatsTripBooking extends Model
{

    protected $guarded = [];

    public function trip()
    {
        return $this->belongsTo(SeatsTrip::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pickup()
    {
        return $this->belongsTo(SeatsLineStation::class, 'pickup_id');
    }

    public function dropoff()
    {
        return $this->belongsTo(SeatsLineStation::class, 'dropoff_id');
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

    public function scopeForPeriod($query, $args) 
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
        $now = date('Y-m-d H:i:s');

        switch($args['time']) {
            case 'PAST':
                return $query->where('pickup_time', '<', $now);
            default:
                return $query->where('pickup_time', '>=', $now);
        }

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
