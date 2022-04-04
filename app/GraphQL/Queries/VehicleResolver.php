<?php

namespace App\GraphQL\Queries;

use App\Repository\Queries\VehicleRepositoryInterface;

class VehicleResolver
{
    private $vehicleRepository;

    public function __construct(VehicleRepositoryInterface $vehicleRepository)
    {
        $this->vehicleRepository = $vehicleRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function typeModels($_, array $args)
    {
        return $this->vehicleRepository->typeModels($args);
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function activeVehicle($_, array $args)
    {
        return $this->vehicleRepository->activeVehicle($args);
    }
}
