<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('viewWebSocketsDashboard', function ($user = null) {
            return true;
        });

        // Gate::before(function ($user = null, $ability) {
        //     try {
        //         if (auth('admin')->user()->can[$ability]) 
        //             return true;
        //         else 
        //             return false;
        //     } catch (\Exception $e) {
        //         return true;
        //     }
        // });
    }
}
