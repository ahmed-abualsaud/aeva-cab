<?php

namespace Aeva\Seats\Providers;

#----------------------------------- QUERIES --------------------------------

# Interfaces
use Aeva\Seats\Domain\Repository\Queries\SeatsTripRepositoryInterface;
use Aeva\Seats\Domain\Repository\Queries\SeatsTripUserRepositoryInterface;
use Aeva\Seats\Domain\Repository\Queries\SeatsLineStationRepositoryInterface;
use Aeva\Seats\Domain\Repository\Queries\SeatsTripBookingRepositoryInterface;
use Aeva\Seats\Domain\Repository\Queries\SeatsTripAppTransactionRepositoryInterface;
use Aeva\Seats\Domain\Repository\Queries\SeatsTripPosTransactionRepositoryInterface;
use Aeva\Seats\Domain\Repository\Queries\SeatsTripTerminalTransactionRepositoryInterface;

# Repositories
use Aeva\Seats\Domain\Repository\Eloquent\Queries\SeatsTripRepository;
use Aeva\Seats\Domain\Repository\Eloquent\Queries\SeatsTripUserRepository;
use Aeva\Seats\Domain\Repository\Eloquent\Queries\SeatsLineStationRepository;
use Aeva\Seats\Domain\Repository\Eloquent\Queries\SeatsTripBookingRepository;
use Aeva\Seats\Domain\Repository\Eloquent\Queries\SeatsTripAppTransactionRepository;
use Aeva\Seats\Domain\Repository\Eloquent\Queries\SeatsTripPosTransactionRepository;
use Aeva\Seats\Domain\Repository\Eloquent\Queries\SeatsTripTerminalTransactionRepository;

# ---------------------------------- MUTATIONS -----------------------------------
# Interfaces
use Aeva\Seats\Domain\Repository\Mutations\SeatsLineRepositoryInterface;
use Aeva\Seats\Domain\Repository\Mutations\SeatsTripEventRepositoryInterface;

# Repositories
use Aeva\Seats\Domain\Repository\Eloquent\Mutations\SeatsLineRepository;
use Aeva\Seats\Domain\Repository\Eloquent\Mutations\SeatsTripEventRepository;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
        /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        #----------------------------------- QUERIES ---------------------------------

        $this->app->bind(SeatsTripRepositoryInterface::class, SeatsTripRepository::class);
        $this->app->bind(SeatsTripUserRepositoryInterface::class, SeatsTripUserRepository::class);
        $this->app->bind(SeatsTripBookingRepositoryInterface::class, SeatsTripBookingRepository::class);
        $this->app->bind(SeatsLineStationRepositoryInterface::class, SeatsLineStationRepository::class);
        $this->app->bind(SeatsTripAppTransactionRepositoryInterface::class, SeatsTripAppTransactionRepository::class);
        $this->app->bind(SeatsTripPosTransactionRepositoryInterface::class, SeatsTripPosTransactionRepository::class);
        $this->app->bind(SeatsTripTerminalTransactionRepositoryInterface::class, SeatsTripTerminalTransactionRepository::class);

        # --------------------------------- MUTATIONS --------------------------------

        $this->app->bind(SeatsLineRepositoryInterface::class, SeatsLineRepository::class);
        $this->app->bind(SeatsTripEventRepositoryInterface::class, SeatsTripEventRepository::class);
    }
}