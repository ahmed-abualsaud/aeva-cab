<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\UserTransactionRepository;

class UserTransactionResolver
{
    private $userTransactionRepository;

    public function __construct(UserTransactionRepository $userTransactionRepository)
    {
        $this->userTransactionRepository = $userTransactionRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        return $this->userTransactionRepository->create($args);
    }
}
