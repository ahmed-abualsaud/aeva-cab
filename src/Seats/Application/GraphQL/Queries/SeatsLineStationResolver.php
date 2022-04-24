<?php

namespace Aeva\Seats\Application\GraphQL\Queries;

use Aeva\Seats\Domain\Repository\Queries\SeatsLineStationRepositoryInterface;

class SeatsLineStationResolver
{

    private $seatsLineStationRepository;

    public function __construct(SeatsLineStationRepositoryInterface $seatsLineStationRepository)
    {
        $this->seatsLineStationRepository = $seatsLineStationRepository;
    }
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function nearby($_, array $args)
    {
        return $this->seatsLineStationRepository->nearby($args);
    }
}
