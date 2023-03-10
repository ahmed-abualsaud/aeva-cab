<?php

namespace Aeva\Cab\Application\GraphQL\Mutations;

use Aeva\Cab\Domain\Repository\Eloquent\Mutations\CabRequestTransactionRepository;

class CabRequestTransactionResolver
{
    private $cabRequestTransactionRepository;

    public function __construct(CabRequestTransactionRepository $cabRequestTransactionRepository)
    {
        $this->cabRequestTransactionRepository = $cabRequestTransactionRepository;
    }

    public function create($_, array $args)
    {
        return $this->cabRequestTransactionRepository->create($args);
    }

    public function destroy($_, array $args)
    {
        return $this->cabRequestTransactionRepository->destroy($args);
    }
}