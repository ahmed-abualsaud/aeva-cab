<?php

namespace App\Http\Controllers\DriverApp\Queries;

use App\Repository\Queries\BusinessTripSubscriptionRepositoryInterface;

class BusinessTripSubscriptionController
{

    private $businessTripSubscriptionRepository;
  
    public function __construct(BusinessTripSubscriptionRepositoryInterface $businessTripSubscriptionRepository)
    {
        $this->businessTripSubscriptionRepository = $businessTripSubscriptionRepository;
    }

    public function businessTripSubscribers($trip_id, $status, $station_id = null)
    {
        $args = [
                    'trip_id'    => $trip_id,
                    'status'     => $status
                ];

        if($station_id != null)
            $args['station_id'] = $station_id;
        
        return $this->businessTripSubscriptionRepository->businessTripSubscribers($args);
    }

    public function businessTripUsersStatus($trip_id = null, $station_id = null)
    {
        $args['trip_id'] = $trip_id;

        if($station_id != null)
            $args['station_id'] = $station_id;
        
        return $this->businessTripSubscriptionRepository->businessTripUsersStatus($args);
    }

    public function businessTripUserStatus($trip_id, $user_id)
    {
        return $this->businessTripSubscriptionRepository->businessTripUserStatus(
            [
                'trip_id' => $trip_id,
                'user_id' => $user_id
            ]
        );
    }

}