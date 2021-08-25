<?php

namespace App\Http\Controllers\DriverApp\Queries;

use App\Repository\Queries\CommunicationRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommunicationController
{
    private $communicationRepository;
  
    public function __construct(CommunicationRepositoryInterface $communicationRepository)
    {
        $this->communicationRepository =  $communicationRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function businessTripChatMessages(Request $request, $user_id)
    {
        $request = $request->all();
        $request['user_id'] = $user_id;

        $validator = Validator::make($request, [
            'user_id' => ['exists:users,id'],
            'log_id' => ['required'],
            'is_private' => ['boolean']
        ]);

        if ($validator->fails())
            return response()->json($validator->errors(), 500);

        return $this->communicationRepository->businessTripChatMessages($request);
    }

    public function businessTripPrivateChatUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'log_id' => ['required']
        ]);

        if ($validator->fails())
            return response()->json($validator->errors(), 500);

        return $this->communicationRepository->businessTripPrivateChatUsers($request->all());
    }
}