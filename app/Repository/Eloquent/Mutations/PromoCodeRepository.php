<?php

namespace App\Repository\Eloquent\Mutations;

use App\PromoCode;
use App\PromoCodeUsage;
use App\Exceptions\CustomException;
use App\Repository\Eloquent\BaseRepository;
use App\Repository\Mutations\PromoCodeRepositoryInterface;

use Aeva\Cab\Domain\Models\CabRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PromoCodeRepository extends BaseRepository implements PromoCodeRepositoryInterface
{
    public function __construct(PromoCode $model)
    {
        parent::__construct($model);
    }
    
    public function apply(array $args)
    {
        // check if the promo code is not expired
        try {
            $promoCode = PromoCode::where('name', $args['name'])
                ->where('expires_on', '>', date('Y-m-d'))
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new CustomException(__('lang.invalid_promo_code'));
        }

        // prevent the rider from using two promo codes at the same time.
        $promo_code =  PromoCodeUsage::selectRaw('
                promo_codes.id, 
                promo_codes.name, 
                COUNT(promo_codes.id) as count
            ')
            ->join('promo_codes', 'promo_code_usages.promo_code_id', 'promo_codes.id')
            ->where('user_id', $args['user_id'])
            ->where('expires_on', '>', date('Y-m-d'))
            ->where('promo_code_usages.used', true)
            ->groupBy('promo_codes.name', 'promo_codes.id')
            ->having('count', '<', 4)
            ->first();

        if($promo_code && $promo_code->name != $args['name'] ) {
            throw new CustomException(__('lang.you_already_applyed_another_promo_code').': '.$promo_code->name);
        }

        // check if the premetted number of promo code trips per user is exceeded or not
        $trip_count = PromoCodeUsage::where('promo_code_id', $promoCode->id)
            ->where('user_id', $args['user_id'])
            ->where('used', true)
            ->count();
    
        if ($trip_count >= $promoCode->max_trips) {
            throw new CustomException(__('lang.permitted_numer_of_trips_exceeded'));
        }
    
        $users_count = PromoCodeUsage::select('user_id')
            ->where('promo_code_id', $promoCode->id)
            ->where('used', true)
            ->groupBy('user_id')
            ->get()
            ->count();
    
        // check if the permetted number of users exceeded or not
        if ($trip_count == 0 && $users_count >= $promoCode->max_users) {
            throw new CustomException(__('lang.permitted_number_of_users_exceeded'));
        }

        if(array_key_exists('request_id', $args)) {
            $this->updateCabRequestPromoCode($args['request_id'], $promoCode->id);
        }
    
        $usage = PromoCodeUsage::where('promo_code_id', $promoCode->id)
            ->where('user_id', $args['user_id'])
            ->where('used', false)
            ->first();
        
        if(!$usage) {
            PromoCodeUsage::create(['promo_code_id' => $promoCode->id, 'user_id' => $args['user_id'], 'used' => false]);
        }

        return $promoCode;
    }

    // check on the cab request is exist or not && th promo code has already been applied or not.
    protected function updateCabRequestPromoCode($request_id, $promo_code_id) 
    {
        try {
            $request = CabRequest::findOrFail($request_id);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.request_not_found'));
        }

        if($request->promo_code_id == $promo_code_id) {
            throw new CustomException(__('lang.promocode_has_already_been_applied'));
        }

        $request->update(['promo_code_id' => $promo_code_id]);
    }
}
