<?php

namespace App\Repository\Mutations;

interface PromoCodeRepositoryInterface
{
    public function apply(array $args);
}