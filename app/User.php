<?php

namespace App;

use App\PartnerUser;
use App\Traits\Searchable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, Searchable;
    
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

    public function scopeSort($query) 
    { 
        return $query->orderBy('created_at', 'DESC');
    }

    public function scopeSearch($query, $args) 
    {
        
        if (array_key_exists('searchQuery', $args) && $args['searchQuery']) {
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);
        }

        return $query;
    }

    public function scopeWhereAssignedOrNot($query, $args) 
    {
        $partnerUsers = PartnerUser::select('user_id')
            ->where('partner_id', $args['partner_id']);

        if ($args['assigned']) {
            $query->whereIn('id', $partnerUsers);
        } else {
            $query->whereNotIn('id', $partnerUsers);
        }

        return $query->orderBy('created_at', 'DESC');
    }

    public function scopeWhereUnsubscribed($query, $args) 
    {
        $businessTripUsers = BusinessTripUser::select('user_id')
            ->where('trip_id', $args['trip_id']);

        $query->select('id', 'name', 'avatar', 'phone');

        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            $query->join('partner_users', 'users.id', '=', 'partner_users.user_id')
                ->where('partner_users.partner_id', $args['partner_id'])
                ->whereNotIn('partner_users.user_id', $businessTripUsers);
        } else {
            $query->whereNotIn('id', $businessTripUsers);
        }

        return $query;
    }

    public static function updateSecondaryNumber(string $no)
    {
        try {
            auth('user')
                ->userOrFail()
                ->update(['secondary_no' => $no]);
        } catch (UserNotDefinedException $e) {
            //
        }
    }

    public function scopeUpdateBalance($query, $user_id, $balance)
    {
        return $query->where('id', $user_id)
            ->decrement('wallet_balance', $balance);
    }
}
 