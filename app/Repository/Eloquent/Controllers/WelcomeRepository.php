<?php

namespace App\Repository\Eloquent\Controllers;

class WelcomeRepository
{
    public function index()
    {
        return view('welcome');
    }
}
