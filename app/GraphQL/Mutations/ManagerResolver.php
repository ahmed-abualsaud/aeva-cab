<?php

namespace App\GraphQL\Mutations;

use App\Repository\Mutations\ManagerRepositoryInterface;

class ManagerResolver
{
    private $managerRepository;

    public function  __construct(ManagerRepositoryInterface $managerRepository)
    {
        $this->managerRepository = $managerRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        return $this->managerRepository->create($args);
    }

    public function update($_, array $args)
    {
        return $this->managerRepository->update($args);
    }

    public function login($_, array $args)
    {
        return $this->managerRepository->login($args);
    }

    public function updatePassword($_, array $args)
    {
        return $this->managerRepository->updatePassword($args);
    }

    public function destroy($_, array $args)
    {
        return $this->managerRepository->destroy($args);
    }
}