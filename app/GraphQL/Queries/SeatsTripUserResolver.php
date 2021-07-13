<?php

namespace App\GraphQL\Queries;

use App\Repository\Queries\MainRepositoryInterface;

class SeatsTripUserResolver
{
    private $seatsTripUserRepository;

    public function __construct(MainRepositoryInterface $seatsTripUserRepository)
    {
        $this->seatsTripUserRepository = $seatsTripUserRepository;
    }
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return $this->seatsTripUserRepository->invoke($args);
    }
}
