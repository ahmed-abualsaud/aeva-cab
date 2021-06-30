<?php
namespace App\Repository;

use App\User;
use Illuminate\Support\Collection;

interface BusinessTripAttendanceRepositoryInterface
{
    public function get(array $args): Collection;
}