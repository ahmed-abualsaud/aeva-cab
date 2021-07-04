<?php

namespace App\GraphQL\Queries;

use App\Repository\Queries\MainRepositoryInterface;

class BusinessTripAttendanceResolver
{

    private $businessTripAttendanceRepository;
  
    public function __construct(MainRepositoryInterface $businessTripAttendanceRepository)
    {
        $this->businessTripAttendanceRepository = $businessTripAttendanceRepository;
    }

    public function __invoke($_, array $args)
    {
        return $this->businessTripAttendanceRepository->invoke($args);
    }
}
