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

    public function promoCodeUsage()
    {
        return $this->hasMany(PromoCodeUsage::class, 'promo_code_id');
    }

    public function scopeWhereType($query, $args) 
    {
        if (array_key_exists('type', $args) && $args['type']) {
            return $query->where('type', $args['type']);
        }
 
        return $query;
    }

    public function scopeWhereValid($query, $args) 
    {
        if (array_key_exists('is_valid', $args) && $args['is_valid']) {
            return $query->where('expires_on', '>=', date('Y-m-d'));
        }
 
        return $query;
    }

}
