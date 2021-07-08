<?php

namespace App\Repository\Mutations;

interface BusinessTripStationRepositoryInterface
{
    public function assignUser(array $args);
    public function acceptStation(array $args);
    public function destroy(array $args);
}