<?php

namespace App\Http\Controllers\DriverApp\Mutations;

use App\Repository\Mutations\DriverRepositoryInterface;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;

class DriverController 
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

    public function update(Request $args)
    {
        return $this->driverRepository->update($args->all());
    }

    public function login(Request $args)
    {
        return $this->driverRepository->login($args->all());
    }

    public function updatePassword(Request $args)
    {
        return $this->driverRepository->updatePassword($args->all());
    }
}