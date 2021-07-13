<?php

namespace App\Repository\Mutations;

interface DriverRepositoryInterface
{
    public function assignVehicle(array $args);
    public function unassignVehicle(array $args);
}