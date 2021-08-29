<?php

namespace App\Http\Controllers\Queries;

use App\Repository\Queries\BusinessTripSubscriptionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BusinessTripSubscriptionController
{
    
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
            'status' => ['required', Rule::in(['PICK_UP', 'DROP_OFF'])]
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }
        
        return $this->businessTripSubscriptionRepository->businessTripSubscribers($request);
    }

    public function businessTripUsersStatus(Request $request, $trip_id = null)
    {
        $request = $request->all();
        if ($trip_id)
            $request['trip_id'] = $trip_id;
        
        return $this->businessTripSubscriptionRepository->businessTripUsersStatus($request);
    }

    public function businessTripUserStatus($trip_id, $user_id)
    {
        $request = [
            'trip_id' => $trip_id,
            'user_id' => $user_id
        ];

        $validator = Validator::make($request, [
            'trip_id' => ['required'],
            'user_id' => ['required']
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        return $this->businessTripSubscriptionRepository->businessTripUserStatus($request);
    }

}