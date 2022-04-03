<?php

namespace Qruz\Seats\Domain\Repository\Queries;

Interface SeatsLineStationRepositoryInterface
{
    public function nearby(array $args);
}