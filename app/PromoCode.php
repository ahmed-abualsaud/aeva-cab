<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    protected $connection = 'mysql';

    protected $appends = ['max_usage'];

    public function promoCodeUsage()
    {
        return $this->hasMany(PromoCodeUsage::class, 'promo_code_id');
    }

    public function scopeOfType($query, $args)
    {
        if (array_key_exists('type', $args) && $args['type']) {
            return $query->where('type', $args['type']);
        }

        return $query;
    }

    public function scopeIsValid($query, $args)
    {
        if (array_key_exists('is_valid', $args) && $args['is_valid']) {
            return $query->where('expires_on', '>', date('Y-m-d'));
        }

        return $query;
    }

    public function getMaxUsageAttribute()
    {
        return ($this->max_users * $this->max_trips);
    }
}
