<?php

namespace App\Http\Controllers\Queries;

use App\Driver;
use Illuminate\Http\Request;
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

    public function driverDeviceId($driver_id) {
        try {
            $data = Driver::select('id', 'device_id')
                ->findOrFail($driver_id);

            return $data['device_id'];

       } catch (ModelNotFoundException $e) {
            $response = [
                'success' => false,
                'message' => 'Not Found'
            ];
            return response()->json($response, 404);
       } 
    }

    public function driversDeviceId(Request $request) {
        try {
            return Driver::select('id', 'device_id')
                ->whereIn('id', $request['drivers_ids'])
                ->pluck('device_id')
                ->toArray();

       } catch (ModelNotFoundException $e) {
            $response = [
                'success' => false,
                'message' => 'Not Found'
            ];
            return response()->json($response, 404);
       } 
    }
}