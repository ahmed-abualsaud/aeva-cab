<?php

namespace App\Http\Controllers\Queries;

use App\AevaPayPartner;
use App\Repository\Queries\PartnerRepositoryInterface;

class PartnerController
{

    protected $partnerRepository;

    public function __construct(PartnerRepositoryInterface $partnerRepository)
    {
        $this->partnerRepository = $partnerRepository;
    }

    public function index()
    {
        $partners = AevaPayPartner::query()->paginate(50);
        return response()->json([
           'status'=> true,
           'message'=> 'all partners',
           'info'=> compact('partners')
        ]);

    }

}
