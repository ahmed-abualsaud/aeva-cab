<?php

namespace App\Repository\Mutations;

interface BusinessTripScheduleRepositoryInterface
{
    public function reschedule(array $args);
}