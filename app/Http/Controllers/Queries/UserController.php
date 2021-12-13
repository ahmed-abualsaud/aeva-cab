<?php

namespace App\Http\Controllers\Queries;

use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController 
{
    public function userDeviceId($user_id)
    {
       try {
            $data = User::select('id', 'device_id')
                ->findOrFail($user_id);

            return $data['device_id'];

       } catch (ModelNotFoundException $e) {
            $response = [
                'success' => false,
                'message' => 'Not Found'
            ];
            return response()->json($response, 404);
       } 
    }
}