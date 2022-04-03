<?php

namespace Qruz\Seats\Domain\Repository\Mutations;

interface SeatsLineRepositoryInterface
{
    public function copy(array $args);
}