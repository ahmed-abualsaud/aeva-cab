<?php

namespace App\Http\Controllers;

use App\Repository\Eloquent\Controllers\WelcomeRepository;

class WelcomeController
{
    private $welcomeRepository;

    public function __construct(WelcomeRepository $welcomeRepository)
    {
        $this->welcomeRepository = $welcomeRepository;
    }

    public function index()
    {
        return $this->welcomeRepository->index();
    }
}
