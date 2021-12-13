<?php

namespace App\Http\Controllers\Mutations;

use Illuminate\Http\Request;
use App\Repository\Eloquent\Controllers\UserRepository;

class UserController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handleAvatar(Request $request)
    {
        return $this->userRepository->handleAvatar($request);
    }

    public function getLanguage(Request $request) 
    {
        return $this->userRepository->getLanguage($request);
    }

    public function create(Request $request) 
    {
        return $this->userRepository->create($request);
    }

    public function login(Request $request) 
    {
        return $this->userRepository->login($request);
    }

    public function socialLogin(Request $request) 
    {
        return $this->userRepository->login($request);
    }

}
