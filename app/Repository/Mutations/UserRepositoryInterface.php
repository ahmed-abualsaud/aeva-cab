<?php

namespace App\Repository\Mutations;

interface UserRepositoryInterface
{
    public function createMultipleUsers(array $args);
    public function socialLogin(array $args);
    public function phoneVerification(array $args);
}