<?php

namespace App\GraphQL\Queries;

use App\Repository\Eloquent\Queries\CabRequestTransactionRepository;

class CabRequestTransactionResolver
{
    private $cabRequestTransactionRepository;

    public function __construct(CabRequestTransactionRepository $cabRequestTransactionRepository)
    {
        $this->cabRequestTransactionRepository = $cabRequestTransactionRepository;
    }

    public function stats($_, array $args)
    {
        return $this->cabRequestTransactionRepository->stats($args);
    }
}