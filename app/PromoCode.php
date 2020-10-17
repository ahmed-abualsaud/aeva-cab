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

}
