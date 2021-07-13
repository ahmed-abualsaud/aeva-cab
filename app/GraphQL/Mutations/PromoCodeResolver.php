<?php

namespace App\GraphQL\Mutations;

use App\Repository\Mutations\PromoCodeRepositoryInterface;

class PromoCodeResolver
{
    private $promoCodeRepository;

    public function __construct(PromoCodeRepositoryInterface $promoCodeRepository)
    {
        $this->promoCodeRepository = $promoCodeRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function apply($_, array $args)
    {
        return $this->promoCodeRepository->apply($args);
    }
}
