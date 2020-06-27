<?php

namespace App;

use App\Traits\DateFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CabRequest extends Model
{
    use SoftDeletes;
    use DateFilter;

    protected $guarded = [];
    protected $hidden = ['route_key'];
    
    public function car_type()
    {
        return $this->belongsTo(CarType::class);
    }
    
    public function payment()
    {
        return $this->hasOne(CabRequestPayment::class, 'request_id');
    }

    public function rating()
    {
        return $this->morphOne(Rating::class, 'ratingable');
    }

    public function filter()
    {
        return $this->hasMany(CabRequestFilter::class, 'request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function scopePendingRequest($query, $user_id)
    {
        return $query->where('user_id', $user_id)
            ->whereNotIn('status' , ['CANCELLED', 'COMPLETED', 'SCHEDULED']);
    }

    public function scopeUserUpcomingTrips($query, $user_id)
    {
        return $query->where('cab_requests.user_id', $user_id)
            ->where('cab_requests.status', 'SCHEDULED')
            ->orderBy('cab_requests.created_at','desc')
            ->select('cab_requests.*')
            ->with('car_type','driver');
    }

    public function scopeProviderUpcomingRequest($query, $user_id)
    {
        return $query->where('cab_requests.driver_id', $user_id)
            ->where('cab_requests.status', 'SCHEDULED')
            ->select('cab_requests.*')
            ->with('car_type','user','driver');
    }

    public function scopeUserTripDetails($query, $user_id, $request_id)
    {
        return $query->where('cab_requests.user_id', $user_id)
            ->where('cab_requests.id', $request_id)
            ->where('cab_requests.status', 'COMPLETED')
            ->select('cab_requests.*')
            ->with('payment','car_type','user','driver','rating');
    }

    public function scopeUserUpcomingTripDetails($query, $user_id, $request_id)
    {
        return $query->where('cab_requests.user_id', $user_id)
            ->where('cab_requests.id', $request_id)
            ->where('cab_requests.status', 'SCHEDULED')
            ->select('cab_requests.*')
            ->with('car_type','user','driver');
    }

    public function scopeFilter($query, $args) 
    {    
        if (array_key_exists('status', $args) && $args['status']) {
            $query->where('status', $args['status']);
        }

        if (array_key_exists('period', $args) && $args['period']) {
            $query = $this->dateFilter($args['period'], $query, 'created_at');
        }

        return $query->orderBy('created_at', 'DESC');
    }
}
