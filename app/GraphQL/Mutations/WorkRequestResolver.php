<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\WorkRequestRepository;

class WorkRequestResolver
{
    private $workRequestRepository;

    public function __construct(WorkRequestRepository $workRequestRepository)
    {
        $this->workRequestRepository = $workRequestRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */

    public function create($_, array $args)
    {
        return $this->workRequestRepository->create($args);
    }

    public function update($_, array $args)
    {
        return $this->workRequestRepository->update($args);
    }

    public function changeStatus($_, array $args)
    {
        return $this->workRequestRepository->changeStatus($args);
    }
    
    public function destroy($_, array $args)
    {
        return $this->workRequestRepository->destroy($args);
    }
}
