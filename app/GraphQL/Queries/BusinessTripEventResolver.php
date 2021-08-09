<?php

namespace App\GraphQL\Queries;

use App\Repository\Eloquent\Queries\BusinessTripEventRepository;

class BusinessTripEventResolver
{

    private $businessTripEventRepository;
  
    public function __construct(BusinessTripEventRepository $businessTripEventRepository)
    {
        $this->businessTripEventRepository = $businessTripEventRepository;
    }

    public function index($_, array $args)
    {
        return $this->businessTripEventRepository->index($args);
    }
}
