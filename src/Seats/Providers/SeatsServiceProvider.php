<?php

namespace Qruz\Seats\Providers;

use Illuminate\Support\ServiceProvider;

class SeatsServiceProvider extends ServiceProvider 
{    
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() 
    {
        $this->loadRoutesFrom(__DIR__.'/../Application/Routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/../Infrastructure/Database/Migrations');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register() 
    {
        $this->app->register(RepositoryServiceProvider::class);
        $this->app->register(GraphqlSchemaServiceProvider::class);
    }
}
