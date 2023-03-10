<?php

namespace App\Http\Controllers\Queries;

use App\Driver;
use App\Traits\HandleAccessTokenCache;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DriverController
{
    use HandleAccessTokenCache;
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

    public function getByPhone($phone, Request $req)
    {
        $str = config('custom.aevacab_staging_server_key').$phone;
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
            $data = Driver::where('phone', $phone)->firstOrFail();
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

    public function BlockedLoggedOut()
    {
        $blocked_ids = Driver::query()->select('id')->where('active_status','=','Blocked')->cursor()->pluck('id');
        $blocked_ids->each(fn($id) => $this->logOutOldDevices('driver',$id));
        return dashboard_success('Blocked drivers tokens invalidated');
    }

    public function AllLoggedOut()
    {
        $all_ids = Driver::query()->select('id')->cursor()->pluck('id');
        $all_ids->each(fn($id) => $this->logOutOldDevices('driver',$id));
        return dashboard_success('All drivers tokens invalidated');
    }
}
