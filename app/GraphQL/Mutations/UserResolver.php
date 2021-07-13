<?php

namespace App\GraphQL\Mutations;

use App\Repository\Mutations\UserRepositoryInterface;
 
class UserResolver
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        return $this->userRepository->create($args);
    }

    public function createMultipleUsers($_, array $args)
    {
        return $this->userRepository->createMultipleUsers($args);
    }

    public function update($_, array $args)
    {
        return $this->userRepository->update($args);
    }

    public function login($_, array $args)
    {
        return $this->userRepository->login($args);
    } 

    public function socialLogin($_, array $args)
    {
        return $this->userRepository->socialLogin($args);
    }

    public function phoneVerification($_, array $args)
    {
        return $this->userRepository->phoneVerification($args);
    }

    public function updatePassword($_, array $args)
    {
        return $this->userRepository->updatePassword($args);
    }

    public function destroy($_, array $args)
    {
        return $this->userRepository->destroy($args);
    }
}