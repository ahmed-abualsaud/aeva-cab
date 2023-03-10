<?php

namespace App; 
 
use Tymon\JWTAuth\Contracts\JWTSubject;

use App\Traits\Searchable;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Notifications\ResetPassword as ResetPasswordNotification;

class Partner extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use Searchable;
    use SoftDeletes;

    protected $guarded = [];

    protected $table = 'credit_go_partners';

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
        $this->notify(new ResetPasswordNotification($token, "partners"));
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

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function drivers()
    {
        return $this->belongsToMany(Driver::class, 'partner_drivers');
    }

    public function trips()
    {
        return $this->hasMany(BusinessTrip::class);
    }

    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords($value);
    }
    
    public function scopeOfType($query, $args) 
    {
        if (array_key_exists('type', $args) && $args['type']) {
            return $query->where('type', $args['type']);
        }
 
        return $query;
    }

    public function scopeGetPaymobID($query, $id)
    {
        return $query->select('paymob_id')
            ->find($id)
            ->paymob_id;
    }

    public function scopeSearch($query, $args) 
    {   
        if (array_key_exists('searchQuery', $args) && $args['searchQuery']) {
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);
        }
    }
}
