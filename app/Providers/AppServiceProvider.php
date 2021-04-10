<?php

namespace App\Providers;

use App\BusinessTrip;
use App\Observers\BusinessTripObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;
use GeneaLabs\LaravelSignInWithApple\Providers\SignInWithAppleProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        $this->bootSocialiteDriver();
        BusinessTrip::observe(BusinessTripObserver::class);
    }

    public function bootSocialiteDriver()
    {
        $socialite = $this->app->make(Factory::class);
        $socialite->extend(
            'apple',
            function ($app) use ($socialite) {
                $config = $app['config']['services.apple'];

                return $socialite
                    ->buildProvider(SignInWithAppleProvider::class, $config);
            }
        );
    }
}
