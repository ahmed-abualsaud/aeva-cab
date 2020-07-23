<?php

namespace App\Http\Controllers;

use Auth;
use App\Fleet;
use Exception;
use App\Driver;
use Carbon\Carbon;
use App\CabRequest;
use App\DriverVehicle;
use App\CabRequestFilter;
use App\Traits\UploadFile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use App\Events\DriverLocationUpdated; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DriverController extends Controller
{
    use UploadFile;

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

    public function handleAvatar(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric',
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $driver = Driver::findOrFail($request->id);
        } catch (ModelNotFoundException $e) {
            return response()->json('The provided driver ID is not found.', 500);
        }

        if ($driver->avatar) $this->deleteOneFile($driver->avatar, 'avatars');
        $url = $this->uploadOneFile($request->avatar, 'avatars');

        $driver->update(['avatar' => $url]);

        return response()->json($driver);
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

        CabRequestFilter::where('driver_id', $driver->id)->delete();

        $driverVehicle = DriverVehicle::where('driver_id', $driver->id);

        $input = ['status' => $request->service_status];

        if ($request->service_status == 'offline') $driverVehicle->where('trip_type', 'CAB');

        $driverVehicle->update($input);

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
