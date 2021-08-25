<?php

namespace App\Http\Controllers\DriverApp\Queries;

use App\Repository\Queries\BusinessTripSubscriptionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\HandleValidatorMessages;

class BusinessTripSubscriptionController
{
    use HandleValidatorMessages;

    private $businessTripSubscriptionRepository;
  
    public function __construct(BusinessTripSubscriptionRepositoryInterface $businessTripSubscriptionRepository)
    {
        $this->businessTripSubscriptionRepository = $businessTripSubscriptionRepository;
    }

    public function businessTripSubscribers(Request $request, $trip_id)
    {
        $request = $request->all();
        $request['trip_id'] = $trip_id;
        
        $validator = Validator::make($request, [
            'trip_id' => ['required', 'exists:business_trip_users,trip_id'],
            'status' => ['required', Rule::in(['PICK_UP', 'DROP_OFF'])],
            'station_id' => ['exists:business_trip_stations,id']
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);
        
        return $this->businessTripSubscriptionRepository->businessTripSubscribers($request);
    }

    public function businessTripUsersStatus(Request $request, $trip_id = null)
    {
        $request = $request->all();

        if($trip_id != null)
            $request['trip_id'] = $trip_id;

        $validator = Validator::make($request, [
            'trip_id' => ['exists:business_trip_users,trip_id'],
            'station_id' => ['exists:business_trip_users,station_id']
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);
        
        return $this->businessTripSubscriptionRepository->businessTripUsersStatus($request);
    }

    public function businessTripUserStatus($trip_id, $user_id)
    {
        $request = [
            'trip_id' => $trip_id,
            'user_id' => $user_id
        ];

        $validator = Validator::make($request, [
            'trip_id' => ['required', 'exists:business_trip_users,trip_id'],
            'user_id' => ['required', 'exists:business_trip_users,user_id']
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);

        return $this->businessTripSubscriptionRepository->businessTripUserStatus($request);
    }

}