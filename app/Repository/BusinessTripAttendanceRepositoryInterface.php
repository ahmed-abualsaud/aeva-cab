<?php
namespace App\Repository;

use Illuminate\Support\Collection;

interface BusinessTripAttendanceRepositoryInterface
{
    public function get(array $args): Collection;
}