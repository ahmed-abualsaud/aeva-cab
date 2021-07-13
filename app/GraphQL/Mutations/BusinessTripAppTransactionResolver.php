<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\BusinessTripAppTransactionRepository;

class BusinessTripAppTransactionResolver
{
    private $businessTripAppTransactionRepository;

    public function __construct(BusinessTripAppTransactionRepository $businessTripAppTransactionRepository)
    {
        $this->businessTripAppTransactionRepository = $businessTripAppTransactionRepository;
    }
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        return $this->businessTripAppTransactionRepository->create($args);
    }

    public function destroy($_, array $args)
    {
        return $this->businessTripAppTransactionRepository->destroy($args);
    }
}
