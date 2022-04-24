<?php

namespace Aeva\Seats\Application\GraphQL\Mutations;

use Aeva\Seats\Domain\Repository\Eloquent\Mutations\SeatsLineStationRepository;

class SeatsLineStationResolver
{
    private $seatsLineStationRepository;

    public function __construct(SeatsLineStationRepository $seatsLineStationRepository)
    {
        $this->seatsLineStationRepository = $seatsLineStationRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function updateRoute($_, array $args)
    {
        return $this->seatsLineStationRepository->updateRoute($args);
    }
}
