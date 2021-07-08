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
            $promoCode = $this->model->where('name', $args['name'])
                ->where('expires_on', '>', date('Y-m-d'))
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new CustomException(__('lang.invalid_promo_code'));
        }

        try {
            $exceeded = PromoCodeUsage::where('promo_code_id', $promoCode->id)
                ->where('user_id', $args['user_id'])
                ->count() 
                == $promoCode->usage;

            if ($exceeded) 
                throw new \Exception(__('lang.permitted_usage_exceeded'));

            PromoCodeUsage::create(['promo_code_id' => $promoCode->id, 'user_id' => $args['user_id']]);
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }

        return $promoCode;
    }
}
