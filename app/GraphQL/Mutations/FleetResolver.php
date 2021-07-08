<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\FleetRepository;

class FleetResolver
{ 
    private $fleetRepository;

    public function  __construct(FleetRepository $fleetRepository)
    {
        $this->fleetRepository = $fleetRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        return $this->fleetRepository->create($args);
    }

    public function update($_, array $args)
    {
        return $this->fleetRepository->update($args);
    }
}
