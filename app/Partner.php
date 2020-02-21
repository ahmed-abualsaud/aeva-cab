<?php

namespace App; 
 
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Partner extends Authenticatable implements JWTSubject
{
    use Notifiable;
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

    public function users()
    {
        return $this->hasMany(PartnerUser::class);
    }

    public function drivers()
    {
        return $this->belongsToMany(Driver::class, 'partner_drivers');
    }

    public function trips()
    {
        return $this->hasMany(PartnerTrip::class);
    }
}
