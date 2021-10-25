<?php

namespace App\Http\Controllers\Queries;

use App\Repository\Queries\PaymentCategoryRepositoryInterface;

class PaymentCategoryController
{
    
    private $paymentCategoryRepository;
  
    public function __construct(PaymentCategoryRepositoryInterface $paymentCategoryRepository)
    {
        $this->paymentCategoryRepository = $paymentCategoryRepository;
    }

    public function partnerPaymentCategories($partner_id)
    {
        return [
            'success' => true,
            'message' => 'Partner Payment Categories',
            'data' => $this->paymentCategoryRepository->partnerPaymentCategories(['partner_id' => $partner_id])
        ];
    }

}