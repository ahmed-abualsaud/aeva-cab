<?php

namespace App\Providers;

use App\Extensions\CachingAdminProvider;
use App\Extensions\CachingDriverProvider;
use App\Extensions\CachingPartnerProvider;
use App\Extensions\CachingManagerProvider;
use App\Extensions\CachingUserProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use App\Guards\RemoteUserGuard;
use App\Guards\RemoteAdminGuard;

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

        $this->app['auth']->provider('cached-user',
            function($app, array $config) {
                return new CachingUserProvider(
                    $this->app['hash'],
                    $config['model']
                );
            });
        
        $this->app['auth']->provider('cached-driver',
            function($app, array $config) {
                return new CachingDriverProvider(
                    $this->app['hash'],
                    $config['model']
                );
            });

        $this->app['auth']->provider('cached-admin',
            function($app, array $config) {
                return new CachingAdminProvider(
                    $this->app['hash'],
                    $config['model']
                );
            });

        $this->app['auth']->provider('cached-partner',
            function($app, array $config) {
                return new CachingPartnerProvider(
                    $this->app['hash'],
                    $config['model']
                );
            });

        $this->app['auth']->provider('cached-manager',
            function($app, array $config) {
                return new CachingManagerProvider(
                    $this->app['hash'],
                    $config['model']
                );
            });

        $this->app['auth']->extend('remote-user', 
            function ($app, $name, array $config) {
                $guard = new RemoteUserGuard($app['tymon.jwt'],$app['request']);
                $app->refresh('request', $guard, 'setRequest');
                return $guard;
            }
        );

        $this->app['auth']->extend('remote-admin', 
            function ($app, $name, array $config) {
                $guard = new RemoteAdminGuard($app['tymon.jwt'],$app['request']);
                $app->refresh('request', $guard, 'setRequest');
                return $guard;
            }
        );

        // Gate::before(function ($user = null, $ability) {
        //     try {
        //         if ($user->role->permissions[$ability]) 
        //             return true;
        //         else 
        //             return false;
        //     } catch (\Exception $e) {
        //         return true;
        //     }
        // });
    }
}
