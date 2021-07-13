<?php

namespace App\GraphQL\Queries;

use App\Repository\Queries\PartnerRepositoryInterface;

class PartnerResolver
{
    private $partnerRepository;

    public function __construct(PartnerRepositoryInterface $partnerRepository)
    {
        $this->partnerRepository = $partnerRepository;
    }
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function users($_, array $args)
    {
        return $this->partnerRepository->users($args);
    }
}
