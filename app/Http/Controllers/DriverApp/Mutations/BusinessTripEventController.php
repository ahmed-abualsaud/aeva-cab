<?php

namespace App\Http\Controllers\DriverApp\Mutations;

use App\Repository\Mutations\BusinessTripEventRepositoryInterface;
use Illuminate\Http\Request;

class BusinessTripEventController 
{
    private $businessTripEventRepository;

    public function __construct(BusinessTripEventRepositoryInterface $businessTripEventRepository)
    {
        $this->businessTripEventRepository = $businessTripEventRepository;
    }

    public function ready(Request $args)
    {
        return $this->businessTripEventRepository->ready($args->all());
    }

    public function start(Request $args)
    {
        return $this->businessTripEventRepository->start($args->all());
    }

    public function atStation(Request $args)
    {
        return $this->businessTripEventRepository->atStation($args->all());
    }

    public function changeAttendanceStatus(Request $args)
    {
        return $this->businessTripEventRepository->changeAttendanceStatus($args->all());
    }

    public function pickUsers(Request $args)
    {
        return $this->businessTripEventRepository->pickUsers($args->all());
    }

    public function dropUsers(Request $args)
    {
        return $this->businessTripEventRepository->dropUsers($args->all());
    }

    public function updateDriverLocation(Request $args)
    {
        return $this->businessTripEventRepository->updateDriverLocation($args->all());
    }

    public function end(Request $args)
    {
        return $this->businessTripEventRepository->end($args->all());
    }
}