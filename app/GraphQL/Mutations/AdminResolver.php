<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\AdminRepository;

class AdminResolver
{
    private $adminRepository;

    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

     /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        return $this->adminRepository->create($args);
    }

    public function update($_, array $args)
    {
        return $this->adminRepository->update($args);
    }

    public function login($_, array $args)
    {
        return $this->adminRepository->login($args);
    }

    public function updatePassword($_, array $args)
    {
        return $this->adminRepository->updatePassword($args);
    }

}
