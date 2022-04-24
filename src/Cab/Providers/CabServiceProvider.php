<?php

namespace Aeva\Cab\Providers;

use Illuminate\Support\ServiceProvider;

class CabServiceProvider extends ServiceProvider 
{    
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() 
    {
        $this->loadMigrationsFrom(__DIR__.'/../Infrastructure/Database/Migrations');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register() 
    {
        $this->app->register(GraphqlSchemaServiceProvider::class);
    }
}
