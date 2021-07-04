<?php

namespace App\GraphQL\Queries;

use App\Repository\Queries\MainRepositoryInterface;

class BusinessTripScheduleResolver
{

    private $businessTripScheduleRepository;
  
    public function __construct(MainRepositoryInterface $businessTripScheduleRepository)
    {
        $this->businessTripScheduleRepository =  $businessTripScheduleRepository;
    }

    public function __invoke($_, array $args)
    {
        return $this->businessTripScheduleRepository->invoke($args);
    }
}
