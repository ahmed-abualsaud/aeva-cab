<?php
namespace App\Repository\Queries;

interface BusinessTripRepositoryInterface
{
    public function userSubscriptions(array $args);
    public function userTrips(array $args);
    public function userLiveTrips(array $args);
    public function driverTrips(array $args);
    public function driverLiveTrips(array $args);
    public function userHistory(array $args);
}