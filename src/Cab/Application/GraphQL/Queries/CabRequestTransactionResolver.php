<?php

namespace Qruz\Cab\Application\GraphQL\Queries;

use Qruz\Cab\Domain\Repository\Eloquent\Queries\CabRequestTransactionRepository;

class CabRequestTransactionResolver
{
    private $cabRequestTransactionRepository;

    public function __construct(CabRequestTransactionRepository $cabRequestTransactionRepository)
    {
        $this->cabRequestTransactionRepository = $cabRequestTransactionRepository;
    }

    public function report($_, array $args)
    {
        return $this->cabRequestTransactionRepository->report($args);
    }
}