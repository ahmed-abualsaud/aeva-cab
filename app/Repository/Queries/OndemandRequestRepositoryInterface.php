<?php

namespace App\Repository\Queries;

interface OndemandRequestRepositoryInterface
{
    public function invoke(array $args);
    public function stats(array $args);
}