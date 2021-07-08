<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\SchoolRequestRepository;

class SchoolRequestResolver
{
    private $schoolRequestRepository;

    public function __construct(SchoolRequestRepository $schoolRequestRepository)
    {
        $this->schoolRequestRepository = $schoolRequestRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */

    public function create($_, array $args)
    {
        return $this->schoolRequestRepository->create($args);
    }

    public function update($_, array $args)
    {
        return $this->schoolRequestRepository->update($args);
    }

    public function changeStatus($_, array $args)
    {
        return $this->schoolRequestRepository->changeStatus($args);
    }
    
    public function destroy($_, array $args)
    {
        return $this->schoolRequestRepository->destroy($args);
    }
}
