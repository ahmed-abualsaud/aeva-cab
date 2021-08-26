<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\WorkplaceRepository;

class WorkplaceResolver
{
    private $workplaceRepository;

    public function __construct(WorkplaceRepository $workplaceRepository)
    {
        $this->workplaceRepository = $workplaceRepository;
    }

    public function destroy($_, array $args)
    {
        return $this->workplaceRepository->destroy($args);
    }
}
