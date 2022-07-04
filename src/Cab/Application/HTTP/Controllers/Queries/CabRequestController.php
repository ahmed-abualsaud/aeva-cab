<?php

namespace Aeva\Cab\Application\Http\Controllers\Queries;

use App\User;
use Aeva\Cab\Domain\Models\CabRequest;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CabRequestController 
{
    public function liveCabTrips($user_id, Request $req) 
    {
        $str = config('custom.aevacab_staging_server_key').$user_id;
        $hashed_str = hash("sha256",$str,true);
        $encoded_str = base64_encode($hashed_str);

        if($req->header('x-api-key') != $encoded_str) {
            $response = [
                'success' => false,
                'message' => 'Unauthorized'
            ];
            return response()->json($response, 401);
        }

        try {
            User::findOrFail($user_id);
            $data = CabRequest::live($user_id)->first();
            $response = [
                'success' => true,
                'data' => $data,
                'message' => 'User Live Cab Trip'
            ];

            return $response;
       } catch (ModelNotFoundException $e) {
            $response = [
                'success' => false,
                'message' => 'User Not Found'
            ];
            return response()->json($response, 404);
       }
    }
}