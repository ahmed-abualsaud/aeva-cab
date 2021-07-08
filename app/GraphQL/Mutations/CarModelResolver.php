<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\CarModelRepository;

class CarModelResolver 
{
    private $carModelRepository;

    public function  __construct(CarModelRepository $carModelRepository)
    {
        $this->carModelRepository = $carModelRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        return $this->carModelRepository->create($args);
    }

    public function update($_, array $args)
    {
        return $this->carModelRepository->update($args);
    }
}