<?php

namespace App\Providers;

use App\Repository\EloquentRepositoryInterface; 
use App\Repository\BusinessTripAttendanceRepositoryInterface; 
use App\Repository\Eloquent\BusinessTripAttendanceRepository; 
use App\Repository\Eloquent\BaseRepository; 

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(EloquentRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(BusinessTripAttendanceRepositoryInterface::class, BusinessTripAttendanceRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
