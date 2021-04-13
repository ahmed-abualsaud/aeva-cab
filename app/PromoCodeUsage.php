<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PromoCodeUsage extends Model
{
    protected $guarded = [];

    public $incrementing = false;

    public $timestamps = false;

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class, 'promo_code_id');
    }

}
