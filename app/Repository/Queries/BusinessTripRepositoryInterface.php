<?php
namespace App\Repository\Queries;

use Illuminate\Support\Collection;

interface BusinessTripRepositoryInterface
{
    public function userSubscriptions(array $args): Collection;
    public function userTrips(array $args): Collection;
    public function userLiveTrips(array $args): Collection;
    public function driverTrips(array $args): Collection;
    public function driverLiveTrips(array $args): Collection;
}