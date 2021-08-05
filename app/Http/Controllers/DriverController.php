<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\Eloquent\Controllers\DriverRepository;

class DriverController
{
    private $driverRepository;

    public function __construct(DriverRepository $driverRepository)
    {
        $this->driverRepository = $driverRepository;
    }

    public function handleAvatar(Request $request)
    {
        return $this->driverRepository->handleAvatar($request);
    }

}
