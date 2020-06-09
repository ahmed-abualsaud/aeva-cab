<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PromoCodeUsage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['updated_at', 'created_at'
    ];

    public function promoCode()
    {
        return $this->belongsTo('App\PromoCode', 'promo_code_id')->withTrashed();
    }

}
