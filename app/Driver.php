<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Authenticatable implements JWTSubject
{
    use SoftDeletes;
    
    protected $guarded = [];

    protected $hidden = ['password'];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function fleet()
    {
        return $this->belongsTo(Fleet::class);
    }

    public function trips()
    {
        return $this->hasMany(PartnerTrip::class);
    }

    public function vehicle()
    {
        return $this->hasOne(DriverVehicle::class, 'driver_id');
    }

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'driver_vehicles');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function deviceTokens()
    {
        return $this->morphMany(DeviceToken::class, 'tokenable');
    }

    public function incoming_requests()
    {
        return $this->hasMany(RequestFilter::class, 'driver_id')
            ->where('status', 0);
    }

    public function requests()
    {
        return $this->hasMany(RequestFilter::class, 'driver_id');
    }

    public function accepted()
    {
        return $this->hasMany(UserRequest::class, 'driver_id')
            ->where('status','!=','CANCELLED');
    }

    public function cancelled()
    {
        return $this->hasMany(UserRequest::class, 'driver_id')
            ->where('status', 'CANCELLED');
    }
} 
