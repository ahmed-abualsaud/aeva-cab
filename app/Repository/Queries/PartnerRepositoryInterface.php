<?php

namespace App\Repository\Queries;

Interface PartnerRepositoryInterface
{
    public function users(array $args);
    public function partnerPaymentCategories(array $args);
}