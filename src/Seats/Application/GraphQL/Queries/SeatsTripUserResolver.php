<?php

namespace Qruz\Seats\Application\GraphQL\Queries;

use Qruz\Seats\Domain\Repository\Queries\SeatsTripUserRepositoryInterface;

class SeatsTripUserResolver
{
    private $seatsTripUserRepository;

    public function __construct(SeatsTripUserRepositoryInterface $seatsTripUserRepository)
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
