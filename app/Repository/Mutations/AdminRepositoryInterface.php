<?php

namespace App\Repository\Mutations;

interface AdminRepositoryInterface
{
    public function updatePassword(array $args);
    public function invalidateToken(array $args);
}