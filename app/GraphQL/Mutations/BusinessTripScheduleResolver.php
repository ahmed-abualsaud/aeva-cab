<?php

namespace App\GraphQL\Mutations;

use App\Repository\Mutations\BusinessTripScheduleRepositoryInterface;


class BusinessTripScheduleResolver
{
    private $businessTripScheduleRepository;

    public function  __construct(BusinessTripScheduleRepositoryInterface $businessTripScheduleRepository)
    {
        $this->businessTripScheduleRepository = $businessTripScheduleRepository;
    }
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function reschedule($_, array $args)
    {
        return $this->businessTripScheduleRepository->reschedule($args);
    }
}
