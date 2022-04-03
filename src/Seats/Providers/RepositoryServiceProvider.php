<?php

namespace Qruz\Seats\Providers;

#----------------------------------- QUERIES --------------------------------

# Interfaces
use Qruz\Seats\Domain\Repository\Queries\SeatsTripRepositoryInterface;
use Qruz\Seats\Domain\Repository\Queries\SeatsTripUserRepositoryInterface;
use Qruz\Seats\Domain\Repository\Queries\SeatsLineStationRepositoryInterface;
use Qruz\Seats\Domain\Repository\Queries\SeatsTripBookingRepositoryInterface;
use Qruz\Seats\Domain\Repository\Queries\SeatsTripAppTransactionRepositoryInterface;
use Qruz\Seats\Domain\Repository\Queries\SeatsTripPosTransactionRepositoryInterface;
use Qruz\Seats\Domain\Repository\Queries\SeatsTripTerminalTransactionRepositoryInterface;

# Repositories
use Qruz\Seats\Domain\Repository\Eloquent\Queries\SeatsTripRepository;
use Qruz\Seats\Domain\Repository\Eloquent\Queries\SeatsTripUserRepository;
use Qruz\Seats\Domain\Repository\Eloquent\Queries\SeatsLineStationRepository;
use Qruz\Seats\Domain\Repository\Eloquent\Queries\SeatsTripBookingRepository;
use Qruz\Seats\Domain\Repository\Eloquent\Queries\SeatsTripAppTransactionRepository;
use Qruz\Seats\Domain\Repository\Eloquent\Queries\SeatsTripPosTransactionRepository;
use Qruz\Seats\Domain\Repository\Eloquent\Queries\SeatsTripTerminalTransactionRepository;

# ---------------------------------- MUTATIONS -----------------------------------
# Interfaces
use Qruz\Seats\Domain\Repository\Mutations\SeatsLineRepositoryInterface;
use Qruz\Seats\Domain\Repository\Mutations\SeatsTripEventRepositoryInterface;

# Repositories
use Qruz\Seats\Domain\Repository\Eloquent\Mutations\SeatsLineRepository;
use Qruz\Seats\Domain\Repository\Eloquent\Mutations\SeatsTripEventRepository;

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