<?php

namespace App\Http\Controllers\DriverApp\Mutations;

use App\Repository\Eloquent\Mutations\BusinessTripAttendanceRepository;
use Illuminate\Http\Request;

class BusinessTripAttendanceController 
{
    private $businessTripAttendanceRepository;

    public function __construct(BusinessTripAttendanceRepository $businessTripAttendanceRepository)
    {
        $this->businessTripAttendanceRepository = $businessTripAttendanceRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create(Request $args)
    {
        return $this->businessTripAttendanceRepository->create($args->all());
    }
}