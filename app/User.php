<?php

namespace App;
 
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable implements JWTSubject
{
    use SoftDeletes, Notifiable, QueryCacheable;
    
    protected $guarded = [];

    protected $hidden = ['password'];

    public $cacheFor = 3600;

    /**
     * Invalidate the cache automatically
     * upon update in the database.
     *
     * @var bool
     */
    protected static $flushCacheOnUpdate = true;

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @param  string  $type
     * @return void
     */ 
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token, "users"));
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

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function deviceTokens()
    {
        return $this->morphMany(DeviceToken::class, 'tokenable');
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords($value);
    }

    public function scopeFilterByPartner($query, $args) 
    {
        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            $query->where('partner_id', $args['partner_id']);
        }

        return $query->orderBy('created_at', 'DESC');
    }
}
 