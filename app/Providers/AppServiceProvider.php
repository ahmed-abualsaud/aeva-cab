<?php

namespace App\Providers;

use App\SeatsLine;
use App\SeatsTrip;
use App\BusinessTrip;
use App\Observers\SeatsLineObserver;
use App\Observers\SeatsTripObserver;
use Illuminate\Support\Facades\Schema;
use App\Observers\BusinessTripObserver;
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
        SeatsTrip::observe(SeatsTripObserver::class);
        SeatsLine::observe(SeatsLineObserver::class);
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
