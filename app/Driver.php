<?php

namespace App;

use App\PartnerDriver;
use App\Traits\Searchable;
use App\Notifications\ResetPassword as ResetPasswordNotification;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class Driver extends Authenticatable implements JWTSubject
{
    use Notifiable; 
    use Searchable;
    use SoftDeletes;

    protected $guarded = [];

    protected $hidden = ['password'];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @param  string  $type
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token, "drivers"));
    }

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

    public function trips()
    {
        return $this->hasMany(BusinessTrip::class);
    }

    public function partners()
    {
        return $this->belongsToMany(Partner::class, 'partner_drivers')
            ->select('id', 'name', 'logo');
    }

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'driver_vehicles');
    }

    public function car_type()
    {
        return $this->belongsTo(CarType::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }



    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords($value);
    }

    public function scopeFleet($query, $args) 
    {
        if (array_key_exists('fleet_id', $args) && $args['fleet_id']) {
            $query->where('fleet_id', $args['fleet_id']);
        }

        return $query;
    }

    public function scopeAssigned($query, $args) 
    {
        return $query->whereIn('id', PartnerDriver::byPartner($args))->latest();
    }

    public function scopeNotAssigned($query, $args) 
    {
        return $query->whereNotIn('id', PartnerDriver::byPartner($args));
    }

    public function scopeSearch($query, $args) 
    {   
        if (array_key_exists('searchQuery', $args) && $args['searchQuery']) {
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);
        }

        return $query->latest();
    }

    public static function updateLocation(string $lat, string $lng)
    {
        try {
            auth('driver')
                ->userOrFail()
                ->update(['latitude' => $lat, 'longitude' => $lng]);
        } catch (UserNotDefinedException $e) {
            //
        }
    }
} 
