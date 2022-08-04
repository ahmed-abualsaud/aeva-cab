<?php

namespace App\Http\Controllers\Queries;

use App\Partner;
use App\Repository\Queries\PartnerRepositoryInterface;
use Illuminate\Support\Facades\Http;

class PartnerController
{

    protected $partnerRepository;

    public function __construct(PartnerRepositoryInterface $partnerRepository)
    {
        $this->partnerRepository = $partnerRepository;
    }

    public function index()
    {
        return Http::get(config('custom.aevapay_production_server_domain').'/api/v1/contact-us/partners')->json();
    }

    public function cashOut()
    {
        $partners = Partner::query()->paginate(50);
        return response_info('Our Partners',compact('partners'));
    }
}
