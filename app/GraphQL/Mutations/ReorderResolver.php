<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\ReorderRepository;

class ReorderResolver
{
    private $reorderRepository;

    public function __construct(ReorderRepository $reorderRepository)
    {
        $this->reorderRepository = $reorderRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return $this->reorderRepository->invoke($args);
    }
}
