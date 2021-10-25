<?php

namespace App\Repository\Eloquent\Queries;

use App\PaymentCategory;
use App\Repository\Queries\PaymentCategoryRepositoryInterface;
use App\Repository\Eloquent\BaseRepository;

class PaymentCategoryRepository extends BaseRepository implements PaymentCategoryRepositoryInterface
{

    public function __construct(PaymentCategory $model)
    {
        parent::__construct($model);
    }

    public function partnerPaymentCategories(array $args)
    {
        return $this->model->select('value')
            ->where('partner_id', $args['partner_id'])
            ->get();
    }
}