<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\BusinessTripRatingRepository;

class BusinessTripRatingResolver
{
    private $businessTripRatingRepository;

    public function __construct(BusinessTripRatingRepository $businessTripRatingRepository)
    {
        $this->businessTripRatingRepository = $businessTripRatingRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function update($_, array $args)
    {
        return $this->businessTripRatingRepository->update($args);
    }
}
