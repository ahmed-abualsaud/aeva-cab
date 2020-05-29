<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
// use App\Events\DriverLocationUpdated; 

use Auth;
use Exception;
use Carbon\Carbon;

use App\Driver;
use App\CabRequest;
use App\DriverVehicle;
use App\Fleet;
use App\CabRequestFilter;

class DriverController extends Controller
{
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'in:android,ios',
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:drivers',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        try {

            $Driver = $request->only(['name', 'email']);
            $Driver['password'] = bcrypt($request->password);
            $Driver = Driver::create($Driver);

            if($request->has('platform') && $request->has('device_id')) {
                DeviceToken::create([
                    'tokenable_id' => $Driver->id,
                    'tokenable_type' => 'App\Driver',
                    'device_id' => $request->device_id,
                    'platform' => $request->platform,
                ]);
            }

            return $Driver;

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Something went wrong, Please try again later!'], 500);
            }
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $credentials = $request->only('email', 'password');

        if (! $token = auth('driver')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth('driver')->user();

        $user->access_token = $token;

        if($request->has('platform') && $request->has('device_id')) {
            try {
                DeviceToken::where('device_id', $request->device_id)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                DeviceToken::create([
                    'tokenable_id' => $user->id,
                    'tokenable_type' => 'App\Driver',
                    'device_id' => $request->device_id,
                    'platform' => $request->platform,
                ]);
            }
        }

        return response()->json($user);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() 
    {
        try {
            $driver_id = Auth::guard('driver')->user()->id;

            $driver = Driver::with('vehicles.type','fleet')->findOrFail($driver_id);

            return $driver;

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Update latitude and longitude of the user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function location(Request $request)
    { 
        $this->validate($request, [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'request_id' => 'numeric'
        ]);

        $location = [
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ];

        auth('driver')->user()->update($location);

        // if ($request->has('request_id')) {
        //     broadcast(new DriverLocationUpdated($location, 'cab.'.$request->request_id))->toOthers();
        // }

        return response()->json(['message' => 'Driver location has been updated successfully.']);
    }

    /**
     * Toggle service availability of the driver.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function available(Request $request)
    {
        $this->validate($request, [
            'service_status' => 'required|in:active,offline',
        ]);

        $driver = Auth::guard('driver')->user();
        
        if($driver->vehicle) {
            
            $driver_id = $driver->id;
            $OfflineOpenRequest = CabRequestFilter::with(['request.driver','request'])
                ->where('driver_id', $driver_id)
                ->whereHas('request', function($query) use ($driver_id){
                    $query->where('status','SEARCHING');
                    $query->where('current_driver_id','<>',$driver_id);
                    $query->orWhereNull('current_driver_id');
                    })->pluck('id');

            if(count($OfflineOpenRequest)>0) {
                CabRequestFilter::whereIn('id',$OfflineOpenRequest)->delete();
            }   
           
            $driver->vehicle->update(['status' => $request->service_status]);
        } else {
            return response()->json(['error' => 'You account has not been approved for driving']);
        }

        return $driver;
    }

    /**
     * Show drivers daily target.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function target(Request $request)
    {
        try {
            
            $Rides = CabRequest::where('driver_id', Auth::guard('driver')->user()->id)
                    ->where('status', 'COMPLETED')
                    ->where('created_at', '>=', Carbon::today())
                    ->with('payment', 'car_type')
                    ->get();

            return response()->json([
                    'rides' => $Rides,
                    'rides_count' => $Rides->count(),
                    'target' => 0
                ]);

        } catch(Exception $e) {
            return response()->json(['error' => "Something Went Wrong"]);
        }
    }
}
