<?php

namespace App\Repository\Queries;

interface VehicleRepositoryInterface
{
    public function typeModels(array $args);
    public function activeVehicle(array $args);
}