<?php

namespace App\Http\Controllers\DriverApp\Queries;

use App\Repository\Queries\BusinessTripSubscriptionRepositoryInterface;
use Illuminate\Http\Request;

class BusinessTripSubscriptionController
{

    private $businessTripSubscriptionRepository;
  
    public function __construct(BusinessTripSubscriptionRepositoryInterface $businessTripSubscriptionRepository)
    {
        $this->businessTripSubscriptionRepository = $businessTripSubscriptionRepository;
    }

    public function businessTripSubscribers(Request $req, $trip_id)
    {
        $req = $req->all();
        $req['trip_id'] = $trip_id;
        
        return $this->businessTripSubscriptionRepository->businessTripSubscribers($req);
    }

    public function businessTripUsersStatus(Request $req, $trip_id = null)
    {
        $req = $req->all();

        if($trip_id != null)
            $req['trip_id'] = $trip_id;
        
        return $this->businessTripSubscriptionRepository->businessTripUsersStatus($req);
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