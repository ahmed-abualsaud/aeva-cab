<?php

namespace App\Http\Controllers\DriverApp\Mutations;

use App\Repository\Mutations\CommunicationRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommunicationController 
{
    private $communicationRepository;

    public function  __construct(CommunicationRepositoryInterface $communicationRepository)
    {
        $this->communicationRepository = $communicationRepository;
    }

     /**
     * @param  null  $_
     * @param  array<string, mixed>  $request
     */
    public function sendBusinessTripChatMessage(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'sender_type' => ['required'],
            'sender_id' => ['required'],
            'trip_id' => ['required'],
            'trip_name' => ['required'],
            'log_id' => ['required'],
            'message' => ['required']
        ]);

        if ($validator->fails())
            return response()->json($validator->errors(), 500);

        return $this->communicationRepository->sendBusinessTripChatMessage($request->all());
    }
}