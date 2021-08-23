<?php

namespace App\Http\Controllers\DriverApp\Mutations;

use App\Repository\Eloquent\Mutations\SeatsTripBookingRepository;
use Illuminate\Http\Request;

class SeatsTripBookingController 
{
    private $seatsTripBookingRepository;

    public function __construct(SeatsTripBookingRepository $seatsTripBookingRepository)
    {
        $this->seatsTripBookingRepository = $seatsTripBookingRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */

    public function update($_, array $args)
    {
        return $this->seatsTripBookingRepository->update($args);

    }
}