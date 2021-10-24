<?php

namespace App\Http\Controllers\Queries;

use App\Repository\Queries\PartnerRepositoryInterface;

class PartnerController
{
    
    private $partnerRepository;
  
    public function __construct(PartnerRepositoryInterface $partnerRepository)
    {
        $this->partnerRepository = $partnerRepository;
    }

    public function partnerPaymentCategories($partner_id)
    {
        return [
            'success' => true,
            'message' => 'Partner Payment Categories',
            'data' => $this->partnerRepository->partnerPaymentCategories(['partner_id' => $partner_id])
        ];
    }

}