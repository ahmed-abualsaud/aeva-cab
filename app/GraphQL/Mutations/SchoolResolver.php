<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\SchoolRepository;

class SchoolResolver
{
    private $schoolRepository;

    public function __construct(SchoolRepository $schoolRepository)
    {
        $this->schoolRepository = $schoolRepository;
    }

    public function destroy($_, array $args)
    {
        return $this->schoolRepository->destroy($args);
    }
}
