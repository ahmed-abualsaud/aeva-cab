<?php

namespace App\Repository\Mutations;

interface BusinessTripEventRepositoryInterface
{
    public function ready(array $args);
    public function start(array $args);
    public function atStation(array $args);
    public function changePickupStatus(array $args);
    public function changeAttendanceStatus(array $args);
    public function pickUsers(array $args);
    public function dropUsers(array $args);
    public function updateDriverLocation(array $args);
    public function end(array $args);
    public function destroy(array $args);
}