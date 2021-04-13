<?php

namespace App\GraphQL\Mutations;

use App\PromoCode;
use App\PromoCodeUsage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PromoCodeResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function apply($_, array $args)
    {
        try {
            $promoCode = PromoCode::where('name', $args['name'])
                ->where('expires_on', '>', date('Y-m-d'))
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Invalid or expired promo code!');
        }

        try {
            PromoCodeUsage::create(['promo_code_id' => $promoCode->id, 'user_id' => $args['user_id']]);
        } catch (\Exception $e) {
            throw new \Exception('This promo code has already been used!');
        }

        return $promoCode;
    }
}
