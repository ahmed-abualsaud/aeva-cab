<?php

namespace Aeva\Seats\Domain\Repository\Mutations;

interface SeatsTripEventRepositoryInterface
{
    public function ready(array $args);
    public function start(array $args);
    public function updateDriverLocation(array $args);
    public function atStation(array $args);
    public function pickUser(array $args);
    public function dropUser(array $args);
    public function end(array $args);
}