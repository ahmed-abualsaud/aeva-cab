<?php

namespace App\Repository\Mutations;

interface BusinessTripRequestRepositoryInterface
{
    public function createTrip(array $args);
    public function addToTrip(array $args);
}