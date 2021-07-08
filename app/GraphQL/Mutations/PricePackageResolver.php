<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\PricePackageRepository;

class PricePackageResolver
{
    private $pricePackageRepository;

    public function __construct(PricePackageRepository $pricePackageRepository)
    {
        $this->pricePackageRepository = $pricePackageRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        return $this->pricePackageRepository->create($args);
    }

    public function update($_, array $args)
    {
        return $this->pricePackageRepository->update($args);
    }
}
