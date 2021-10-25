<?php

namespace App\Repository\Queries;

Interface PaymentCategoryRepositoryInterface
{
    public function partnerPaymentCategories(array $args);
}