<?php

namespace App\GraphQL\Mutations;

use App\Repository\Mutations\DriverRepositoryInterface;

class DriverResolver 
{
    private $driverRepository;

    public function  __construct(DriverRepositoryInterface $driverRepository)
    {
        $this->driverRepository = $driverRepository;
    }
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        return $this->driverRepository->create($args);
    }

    public function update($_, array $args)
    {
        return $this->driverRepository->update($args);
    }

    public function login($_, array $args)
    {
        return $this->driverRepository->login($args);
    }

    public function updatePassword($_, array $args)
    {
        return $this->driverRepository->updatePassword($args);
    }

    public function assignVehicle($_, array $args)
    {
        return $this->driverRepository->assignVehicle($args);
    }

    public function unassignVehicle($_, array $args)
    {
        return $this->driverRepository->unassignVehicle($args);
    }

    public function destroy($_, array $args)
    {
        return $this->driverRepository->destroy($args);
    }

}