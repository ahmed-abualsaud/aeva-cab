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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUsage($query, $args)
    {
        return $query->selectRaw('
            promo_code_usages.user_id,
            promo_codes.id as promo_code_id,
            promo_codes.name as promo_code_name,
            COUNT(promo_codes.id) as promo_code_count
        ')
        ->join('promo_codes', 'promo_code_usages.promo_code_id', 'promo_codes.id')
        ->join('cab_requests', 'promo_code_usages.promo_code_id', 'cab_requests.promo_code_id')
        ->where('status', 'Completed')
        ->where('promo_code_usages.used', true)
        ->groupBy('promo_codes.id', 'promo_code_usages.user_id');
    }
}
