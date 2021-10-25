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

}