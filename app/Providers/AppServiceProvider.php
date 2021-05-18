<?php

namespace App\Providers;

use App\Admin;
use App\User;
use App\BusinessTrip;
use App\Driver;
use App\Observers\AdminObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Schema;
use App\Observers\BusinessTripObserver;
use App\Observers\DriverObserver;
use App\Observers\PartnerObserver;
use App\Partner;
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

        Admin::observe(AdminObserver::class);
        Partner::observe(PartnerObserver::class);
        User::observe(UserObserver::class);
        Driver::observe(DriverObserver::class);
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
