<?php

namespace Aeva\Seats\Application\GraphQL\Mutations;

use Aeva\Seats\Domain\Repository\Mutations\SeatsLineRepositoryInterface;

class SeatsLineResolver
{
    private $seatsLineRepository;

    public function __construct(SeatsLineRepositoryInterface $seatsLineRepository)
    {
        $this->seatsLineRepository = $seatsLineRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */

    public function copy($_, array $args)
    {
        return $this->seatsLineRepository->copy($args);
    }

    public function updateRoute($_, array $args)
    {
        return $this->seatsLineRepository->updateRoute($args);
    }
}
