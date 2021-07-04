<?php

namespace App\Providers;

# Resolvers
use App\GraphQL\Queries\BusinessTripAttendanceResolver;
use App\GraphQL\Queries\BusinessTripScheduleResolver;
use App\GraphQL\Queries\DocumentResolver;
use App\GraphQL\Queries\SeatsTripUserResolver;

# Interfaces
use App\Repository\Queries\EloquentRepositoryInterface; 
use App\Repository\Queries\MainRepositoryInterface;
use App\Repository\Queries\BusinessTripSubscriptionRepositoryInterface; 
use App\Repository\Queries\BusinessTripRepositoryInterface;
use App\Repository\Queries\CommunicationRepositoryInterface;
use App\Repository\Queries\NotificationRepositoryInterface;
use App\Repository\Queries\OndemandRequestRepositoryInterface;
use App\Repository\Queries\PartnerRepositoryInterface;
use App\Repository\Queries\SeatsLineStationRepositoryInterface;
use App\Repository\Queries\SeatsTripAppTransactionRepositoryInterface;
use App\Repository\Queries\SeatsTripBookingRepositoryInterface;
use App\Repository\Queries\SeatsTripRepositoryInterface;
use App\Repository\Queries\SeatsTripTerminalTransactionRepositoryInterface;
use App\Repository\Queries\VehicleRepositoryInterface;

# Repositories
use App\Repository\Eloquent\Queries\BusinessTripAttendanceRepository; 
use App\Repository\Eloquent\Queries\BusinessTripSubscriptionRepository; 
use App\Repository\Eloquent\Queries\BusinessTripScheduleRepository; 
use App\Repository\Eloquent\Queries\BusinessTripRepository; 
use App\Repository\Eloquent\Queries\CommunicationRepository;
use App\Repository\Eloquent\Queries\DocumentRepository;
use App\Repository\Eloquent\Queries\NotificationRepository;
use App\Repository\Eloquent\Queries\BaseRepository; 
use App\Repository\Eloquent\Queries\OndemandRequestRepository;
use App\Repository\Eloquent\Queries\PartnerRepository;
use App\Repository\Eloquent\Queries\SeatsLineStationRepository;
use App\Repository\Eloquent\Queries\SeatsTripAppTransactionRepository;
use App\Repository\Eloquent\Queries\SeatsTripBookingRepository;
use App\Repository\Eloquent\Queries\SeatsTripRepository;
use App\Repository\Eloquent\Queries\SeatsTripTerminalTransactionRepository;
use App\Repository\Eloquent\Queries\SeatsTripUserRepository;
use App\Repository\Eloquent\Queries\VehicleRepository;

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
        $this->app->bind(BusinessTripSubscriptionRepositoryInterface::class, BusinessTripSubscriptionRepository::class);
        $this->app->bind(BusinessTripRepositoryInterface::class, BusinessTripRepository::class);
        $this->app->bind(CommunicationRepositoryInterface::class, CommunicationRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);
        $this->app->bind(OndemandRequestRepositoryInterface::class, OndemandRequestRepository::class);
        $this->app->bind(PartnerRepositoryInterface::class, PartnerRepository::class);
        $this->app->bind(SeatsLineStationRepositoryInterface::class, SeatsLineStationRepository::class);
        $this->app->bind(SeatsTripAppTransactionRepositoryInterface::class, SeatsTripAppTransactionRepository::class);
        $this->app->bind(SeatsTripBookingRepositoryInterface::class, SeatsTripBookingRepository::class);
        $this->app->bind(SeatsTripRepositoryInterface::class, SeatsTripRepository::class);
        $this->app->bind(SeatsTripTerminalTransactionRepositoryInterface::class, SeatsTripTerminalTransactionRepository::class);
        $this->app->bind(VehicleRepositoryInterface::class, VehicleRepository::class);

        $this->app->when(BusinessTripAttendanceResolver::class)
                  ->needs(MainRepositoryInterface::class)
                  ->give(BusinessTripAttendanceRepository::class);

        $this->app->when(BusinessTripScheduleResolver::class)
                  ->needs(MainRepositoryInterface::class)
                  ->give(BusinessTripScheduleRepository::class);

        $this->app->when(DocumentResolver::class)
                  ->needs(MainRepositoryInterface::class)
                  ->give(DocumentRepository::class);
    
        $this->app->when(SeatsTripUserResolver::class)
                  ->needs(MainRepositoryInterface::class)
                  ->give(SeatsTripUserRepository::class);
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
