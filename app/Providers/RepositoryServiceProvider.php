<?php

namespace App\Providers;

#----------------------------------- QUERIES --------------------------------
# Resolvers
use App\GraphQL\Queries\BusinessTripAttendanceResolver;
use App\GraphQL\Queries\BusinessTripScheduleResolver;
use App\GraphQL\Queries\DocumentResolver;

# Interfaces
use App\Repository\EloquentRepositoryInterface; 
use App\Repository\Queries\MainRepositoryInterface;
use App\Repository\Queries\BusinessTripSubscriptionRepositoryInterface; 
use App\Repository\Queries\BusinessTripRepositoryInterface;
use App\Repository\Queries\CommunicationRepositoryInterface;
use App\Repository\Queries\NotificationRepositoryInterface;
use App\Repository\Queries\OndemandRequestRepositoryInterface;
use App\Repository\Queries\PartnerRepositoryInterface;
use App\Repository\Queries\PaymentCategoryRepositoryInterface;
use App\Repository\Queries\VehicleRepositoryInterface;

# Repositories
use App\Repository\Eloquent\BaseRepository; 
use App\Repository\Eloquent\Queries\BusinessTripAttendanceRepository; 
use App\Repository\Eloquent\Queries\BusinessTripSubscriptionRepository; 
use App\Repository\Eloquent\Queries\BusinessTripScheduleRepository; 
use App\Repository\Eloquent\Queries\BusinessTripRepository; 
use App\Repository\Eloquent\Queries\CommunicationRepository;
use App\Repository\Eloquent\Queries\DocumentRepository;
use App\Repository\Eloquent\Queries\NotificationRepository;
use App\Repository\Eloquent\Queries\OndemandRequestRepository;
use App\Repository\Eloquent\Queries\PartnerRepository;
use App\Repository\Eloquent\Queries\PaymentCategoryRepository;
use App\Repository\Eloquent\Queries\VehicleRepository;

# ---------------------------------- MUTATIONS -----------------------------------
# Interfaces
use App\Repository\Mutations\BusinessTripEventRepositoryInterface;
use App\Repository\Mutations\BusinessTripRequestRepositoryInterface;
use App\Repository\Mutations\BusinessTripRepositoryInterface as BusinessTripRepoInterface;
use App\Repository\Mutations\BusinessTripScheduleRepositoryInterface;
use App\Repository\Mutations\BusinessTripStationRepositoryInterface;
use App\Repository\Mutations\CommunicationRepositoryInterface as CommunicationRepoInterface;
use App\Repository\Mutations\DriverRepositoryInterface;
use App\Repository\Mutations\PartnerRepositoryInterface as PartnerRepoInterface;
use App\Repository\Mutations\ManagerRepositoryInterface;
use App\Repository\Mutations\PaymentRepositoryInterface;
use App\Repository\Mutations\PromoCodeRepositoryInterface;
use App\Repository\Mutations\UserRepositoryInterface;


# Repositories
use App\Repository\Eloquent\Mutations\BusinessTripEventRepository;
use App\Repository\Eloquent\Mutations\BusinessTripRequestRepository;
use App\Repository\Eloquent\Mutations\BusinessTripRepository as BusinessTripRepo;
use App\Repository\Eloquent\Mutations\BusinessTripScheduleRepository as BusinessTripScheduleRepo;
use App\Repository\Eloquent\Mutations\BusinessTripStationRepository;
use App\Repository\Eloquent\Mutations\CommunicationRepository as CommunicationRepo;
use App\Repository\Eloquent\Mutations\DriverRepository;
use App\Repository\Eloquent\Mutations\PartnerRepository as PartnerRepo;
use App\Repository\Eloquent\Mutations\ManagerRepository;
use App\Repository\Eloquent\Mutations\PaymentRepository;
use App\Repository\Eloquent\Mutations\PromoCodeRepository;
use App\Repository\Eloquent\Mutations\UserRepository;

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
        #----------------------------------- QUERIES --------------------------------
        
        $this->app->bind(EloquentRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(BusinessTripSubscriptionRepositoryInterface::class, BusinessTripSubscriptionRepository::class);
        $this->app->bind(BusinessTripRepositoryInterface::class, BusinessTripRepository::class);
        $this->app->bind(CommunicationRepositoryInterface::class, CommunicationRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);
        $this->app->bind(OndemandRequestRepositoryInterface::class, OndemandRequestRepository::class);
        $this->app->bind(PartnerRepositoryInterface::class, PartnerRepository::class);
        $this->app->bind(PaymentCategoryRepositoryInterface::class, PaymentCategoryRepository::class);
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


        # ---------------------------------- MUTATIONS -----------------------------------

        $this->app->bind(BusinessTripEventRepositoryInterface::class, BusinessTripEventRepository::class);
        $this->app->bind(BusinessTripRequestRepositoryInterface::class, BusinessTripRequestRepository::class);
        $this->app->bind(BusinessTripRepoInterface::class, BusinessTripRepo::class);
        $this->app->bind(BusinessTripScheduleRepositoryInterface::class, BusinessTripScheduleRepo::class);
        $this->app->bind(BusinessTripStationRepositoryInterface::class, BusinessTripStationRepository::class);
        $this->app->bind(CommunicationRepoInterface::class, CommunicationRepo::class);
        $this->app->bind(DriverRepositoryInterface::class, DriverRepository::class);
        $this->app->bind(PartnerRepoInterface::class, PartnerRepo::class);
        $this->app->bind(ManagerRepositoryInterface::class, ManagerRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(PromoCodeRepositoryInterface::class, PromoCodeRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

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
