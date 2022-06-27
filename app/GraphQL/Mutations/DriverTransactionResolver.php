<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\DriverTransactionRepository;

class DriverTransactionResolver
{
    private $driverTransactionRepository;

    public function __construct(DriverTransactionRepository $driverTransactionRepository)
    {
        $this->driverTransactionRepository = $driverTransactionRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        return $this->driverTransactionRepository->create($args);
    }
}
