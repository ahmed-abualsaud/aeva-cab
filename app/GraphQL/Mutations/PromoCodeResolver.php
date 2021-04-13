<?php

namespace App\GraphQL\Mutations;

use App\PromoCode;
use App\PromoCodeUsage;
use App\Exceptions\CustomException;
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
            throw new CustomException('Invalid or expired promo code!');
        }

        try {
            $exceeded = PromoCodeUsage::where('promo_code_id', $promoCode->id)
                ->where('user_id', $args['user_id'])
                ->count() 
                == $promoCode->usage;

            if ($exceeded) 
                throw new \Exception('You have exceeded the permitted usage times!');

            PromoCodeUsage::create(['promo_code_id' => $promoCode->id, 'user_id' => $args['user_id']]);
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }

        return $promoCode;
    }
}
