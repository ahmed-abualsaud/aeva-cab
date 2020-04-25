<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRequest extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    
    public function car_type()
    {
        return $this->belongsTo('App\CarType');
    }
    
    public function payment()
    {
        return $this->hasOne('App\UserRequestPayment', 'request_id');
    }

    public function rating()
    {
        return $this->hasOne('App\UserRequestRating', 'request_id');
    }

    public function filter()
    {
        return $this->hasMany('App\RequestFilter', 'request_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function driver()
    {
        return $this->belongsTo('App\Driver');
    }

    public function scopePendingRequest($query, $user_id)
    {
        return $query->where('user_id', $user_id)
            ->whereNotIn('status' , ['CANCELLED', 'COMPLETED', 'SCHEDULED']);
    }

    public function scopeRequestHistory($query)
    {
        return $query->orderBy('user_requests.created_at', 'desc')
            ->with('user','payment','driver');
    }

    public function scopeUserTrips($query, $user_id)
    {
        return $query->where('user_requests.user_id', $user_id)
            ->where('user_requests.status','COMPLETED')
            ->orderBy('user_requests.created_at','desc')
            ->select('user_requests.*')
            ->with('payment','car_type');
    }

    public function scopeUserUpcomingTrips($query, $user_id)
    {
        return $query->where('user_requests.user_id', $user_id)
            ->where('user_requests.status', 'SCHEDULED')
            ->orderBy('user_requests.created_at','desc')
            ->select('user_requests.*')
            ->with('car_type','driver');
    }

    public function scopeProviderUpcomingRequest($query, $user_id)
    {
        return $query->where('user_requests.driver_id', $user_id)
            ->where('user_requests.status', 'SCHEDULED')
            ->select('user_requests.*')
            ->with('car_type','user','driver');
    }

    public function scopeUserTripDetails($query, $user_id, $request_id)
    {
        return $query->where('user_requests.user_id', $user_id)
            ->where('user_requests.id', $request_id)
            ->where('user_requests.status', 'COMPLETED')
            ->select('user_requests.*')
            ->with('payment','car_type','user','driver','rating');
    }

    public function scopeUserUpcomingTripDetails($query, $user_id, $request_id)
    {
        return $query->where('user_requests.user_id', $user_id)
            ->where('user_requests.id', $request_id)
            ->where('user_requests.status', 'SCHEDULED')
            ->select('user_requests.*')
            ->with('car_type','user','driver');
    }

    public function scopeUserRequestStatusCheck($query, $user_id, $check_status)
    {
        return $query->where('user_requests.user_id', $user_id)
            ->where('user_requests.user_rated',0)
            ->whereNotIn('user_requests.status', $check_status)
            ->select('user_requests.*')
            ->with('user','driver','car_type','rating','payment');
    }

    public function scopeUserRequestAssignProvider($query, $user_id, $check_status)
    {
        return $query->where('user_requests.user_id', $user_id)
            ->where('user_requests.user_rated',0)
            ->whereNull('user_requests.driver_id')
            ->where('user_requests.status', $check_status)
            ->select('user_requests.*');
    }
}
