<?php

namespace App\Http\Controllers\Queries;

use App\Driver;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DriverController 
{

    public function auth()
    {
        return auth('driver')->user();
    }

    public function show($id)
    {
       try {
            $data = Driver::findOrFail($id);
            $response = [
                'success' => true,
                'message' => 'Driver Details',
                'data' => $data
            ];
            return $response;
       } catch (ModelNotFoundException $e) {
            $response = [
                'success' => false,
                'message' => 'Not Found'
            ];
            return response()->json($response, 404);
       } 
    }
}