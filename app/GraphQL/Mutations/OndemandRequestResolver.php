<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\OndemandRequestRepository;

class OndemandRequestResolver
{
    private $ondemandRequestRepository;

    public function  __construct(OndemandRequestRepository $ondemandRequestRepository)
    {
        $this->ondemandRequestRepository = $ondemandRequestRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        return $this->ondemandRequestRepository->create($args);
    }

    public function update($_, array $args)
    {
        return $this->ondemandRequestRepository->update($args);
    }

    public function destroy($_, array $args)
    {
        return $this->ondemandRequestRepository->destroy($args);
    }

}
