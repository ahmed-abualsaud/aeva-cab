<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\RoleRepository;

class RoleResolver
{
    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function update($_, array $args)
    {
        return$this->roleRepository->update($args);
    }
}
