<?php

namespace Qruz\Cab\Application\GraphQL\Mutations;

use Qruz\Cab\Domain\Repository\Eloquent\Mutations\CabRatingRepository;

class CabRatingResolver
{
    private $cabRatingRepository;

    public function __construct(CabRatingRepository $cabRatingRepository)
    {
        $this->cabRatingRepository = $cabRatingRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function update($_, array $args)
    {
        return $this->cabRatingRepository->update($args);
    }
}