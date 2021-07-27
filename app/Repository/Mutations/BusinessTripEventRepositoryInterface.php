<?php

namespace App\Repository\Mutations;

interface BusinessTripEventRepositoryInterface
{
    public function changeDriverStatus(array $args);
    public function startTrip(array $args);
    public function atStation(array $args);
    public function changeBusinessTripPickupStatus(array $args);
    public function changeBusinessTripAttendanceStatus(array $args);
    public function pickUsers(array $args);
    public function dropUsers(array $args);
    public function updateDriverLocation(array $args);
    public function endTrip(array $args);
    public function destroy(array $args);
}