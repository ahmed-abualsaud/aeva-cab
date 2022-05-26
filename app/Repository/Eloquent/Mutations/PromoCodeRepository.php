<?php

namespace App\Repository\Eloquent\Mutations;

use App\PromoCode;
use App\PromoCodeUsage;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repository\Eloquent\BaseRepository;
use App\Repository\Mutations\PromoCodeRepositoryInterface;

class PromoCodeRepository extends BaseRepository implements PromoCodeRepositoryInterface
{
    public function __construct(PromoCode $model)
    {
        parent::__construct($model);
    }
    
    public function apply(array $args)
    {
        try {
            $promoCode = PromoCode::where('name', $args['name'])
                ->where('expires_on', '>', date('Y-m-d'))
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new CustomException(__('lang.invalid_promo_code'));
        }
            
        $trip_count = PromoCodeUsage::where('promo_code_id', $promoCode->id)
            ->where('user_id', $args['user_id'])
            ->count() ;
    
        if ($trip_count >= $promoCode->max_trips) {
            throw new CustomException(__('lang.permitted_number_of_trips_exceeded'));
        }
    
        $users_count = PromoCodeUsage::select('user_id')
        ->where('promo_code_id', $promoCode->id)
        ->groupBy('user_id')
        ->get()
        ->count() ;
    
        if ($trip_count == 0 && $users_count >= $promoCode->max_users) {
            throw new CustomException(__('lang.permitted_number_of_users_exceeded'));
        }
    
        PromoCodeUsage::create(['promo_code_id' => $promoCode->id, 'user_id' => $args['user_id']]);

        return $promoCode;
    }
}
