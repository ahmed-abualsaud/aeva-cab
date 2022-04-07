<?php

namespace Qruz\Cab\Domain\Models;

use App\PartnerUser;
use App\Traits\Searchable;
use App\Notifications\ResetPassword as ResetPasswordNotification;

use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

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

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords($value);
    }

    public function scopeSearch($query, $args) 
    {
        if (array_key_exists('searchQuery', $args) && $args['searchQuery']) {
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);
        }
        return $query->latest();
    }

    public function scopeAssignedOrNot($query, $args) 
    {
        if ($args['assigned']) {
            return $query->whereIn('id', PartnerUser::getIds($args));
        } else {
            return $query->whereNotIn('id', PartnerUser::getIds($args));
        }
    }

    public function scopeAssigned($query, $args) 
    {
        return $query->whereIn('id', PartnerUser::getIds($args));
    }

    public function scopeNotAssigned($query, $args) 
    {
        return $query->whereNotIn('id', PartnerUser::getIds($args));
    }

    public function scopeUnsubscribed($query, $args) 
    {
        $businessTripUsers = BusinessTripSubscription::select('user_id')
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

    public function scopeUpdateWallet($query, $user_id, $balance)
    {
        Cache::forget('user.'.$user_id);

        return $query->where('id', $user_id)
            ->decrement('wallet_balance', $balance);
    }

    public function scopeUpdateInsurance($query, $user_id, $balance)
    {
        Cache::forget('user.'.$user_id);

        return $query->where('id', $user_id)
            ->decrement('insurance_balance', $balance);
    }

    public function scopeUpdateNfcBalance($query, $user_id, $balance)
    {
        Cache::forget('user.'.$user_id);

        return $query->where('id', $user_id)
            ->decrement('nfc_balance', $balance);
    }
}
 