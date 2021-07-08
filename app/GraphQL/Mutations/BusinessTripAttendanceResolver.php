<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\BusinessTripAttendanceRepository;

class BusinessTripAttendanceResolver
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
    public function create($_, array $args)
    {
        return $this->businessTripAttendanceRepository->create($args);
    }

}
