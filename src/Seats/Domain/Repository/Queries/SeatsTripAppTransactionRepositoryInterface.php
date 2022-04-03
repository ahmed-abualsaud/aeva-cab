<?php

namespace Qruz\Seats\Domain\Repository\Queries;

interface SeatsTripAppTransactionRepositoryInterface
{
    public function stats(array $args);
}