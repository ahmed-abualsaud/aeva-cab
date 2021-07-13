<?php

namespace App\GraphQL\Queries;

use App\Repository\Queries\OndemandRequestRepositoryInterface;

class OndemandRequestResolver
{
    private $ondemandRequestRepository;

    public function __construct(OndemandRequestRepositoryInterface $ondemandRequestRepository)
    {
        $this->ondemandRequestRepository = $ondemandRequestRepository;
    }
    
    public function __invoke($_, array $args)
    {
        return $this->ondemandRequestRepository->invoke($args);
    }

    public function stats($_, array $args)
    {
        return $this->ondemandRequestRepository->stats($args);
    }
}
