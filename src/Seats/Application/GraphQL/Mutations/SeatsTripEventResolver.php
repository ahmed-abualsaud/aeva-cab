<?php

namespace Aeva\Seats\Application\GraphQL\Mutations;

use Aeva\Seats\Domain\Repository\Mutations\SeatsTripEventRepositoryInterface;

class SeatsTripEventResolver
{
    private $seatsTripEventRepository;

    public function __construct(SeatsTripEventRepositoryInterface $seatsTripEventRepository)
    {
        $this->seatsTripEventRepository = $seatsTripEventRepository;
    }

    public function ready($_, array $args)
    {
        return $this->seatsTripEventRepository->ready($args);
    }

    public function start($_, array $args)
    {
        return $this->seatsTripEventRepository->start($args);
    }

    public function updateDriverLocation($_, array $args)
    {
        return $this->seatsTripEventRepository->updateDriverLocation($args);
    }

    public function atStation($_, array $args)
    {
        return $this->seatsTripEventRepository->atStation($args);
    }

    public function pickUser($_, array $args)
    {
        return $this->seatsTripEventRepository->pickUser($args);
    }

    public function dropUser($_, array $args)
    {
        return $this->seatsTripEventRepository->dropUser($args);
    }

    public function end($_, array $args)
    {
        return $this->seatsTripEventRepository->end($args);
    }

    public function destroy($_, array $args)
    {
        return $this->seatsTripEventRepository->destroy($args);
    }
}
