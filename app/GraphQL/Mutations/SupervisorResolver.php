<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\SupervisorRepository;
 
class SupervisorResolver
{
    private $supervisorRepository;

    public function __construct(SupervisorRepository $supervisorRepository)
    {
        $this->supervisorRepository = $supervisorRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        return $this->supervisorRepository->create($args);
    }

    public function update($_, array $args)
    {
        return $this->supervisorRepository->update($args);
    }

    public function destroy($_, array $args)
    {
        return $this->supervisorRepository->destroy($args);
    }
}