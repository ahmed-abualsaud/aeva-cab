<?php

namespace Aeva\Seats\Domain\Repository\Queries;

interface SeatsTripAppTransactionRepositoryInterface
{
    public function stats(array $args);
}