<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use DB;
use Auth;
use Hash;
use Storage;

use Exception;
use Notification;
use Carbon\Carbon;

use App\Card;
use App\User;
use App\Driver;
use App\PromoCode;
use App\DeviceToken;
use App\CarType;
use App\UserRequest;
use App\RequestFilter;
use App\PromoCodeUsage;
use App\WalletPassbook;
use App\DriverVehicle;
use App\UserRequestRating;
use App\Http\Controllers\TripController;
use App\Http\Controllers\SendPushController;

class RiderController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'in:android,ios',
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        try {
            $User = $request->only(['name', 'email']);
            $User['payment_mode'] = 'CASH';
            $User['password'] = bcrypt($request->password);
            $User = User::create($User);

            if($request->has('platform') && $request->has('device_id')) { 
                DeviceToken::create([
                    'tokenable_id' => $User->id,
                    'tokenable_type' => 'App\User',
                    'device_id' => $request->device_id,
                    'platform' => $request->platform,
                ]);
            }
            return $User;
        } catch (Exception $e) {
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

        if (! $token = auth('user')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth('user')->user();

        $user->access_token = $token;

        if($request->has('platform') && $request->has('device_id')) {
            try {
                DeviceToken::where('device_id', $request->device_id)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                DeviceToken::create([
                    'tokenable_id' => $user->id,
                    'tokenable_type' => 'App\User',
                    'device_id' => $request->device_id,
                    'platform' => $request->platform,
                ]);
            }
        }

        return response()->json($user);
    }

    public function logout(Request $request)
    {
        auth('user')->logout();
        return response()->json(['message' => trans('cabResponses.logout_success')]);
    }

    public function update_location(Request $request){

        $this->validate($request, [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = Auth::guard('user')->user();
        $user->update([
            "latitude" => $request->latitude,
            "longitude" => $request->longitude
        ]);
        return response()->json(['message' => trans('cabResponses.user.location_updated')]);
    }

    public function details(Request $request)
    {
        $this->validate($request, [
            'platform' => 'in:android,ios',
        ]);

        try {
            return User::find(Auth::guard('user')->user()->id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => trans('cabResponses.user.user_not_found')], 500);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function services() {

        if ($serviceList = CarType::all()) {
            return $serviceList;
        } else {
            return response()->json(['error' => trans('cabResponses.services_not_found')], 500);
        }

    }

    public function send_request(Request $request) 
    {
        $distance = 10;
        $surge_factor = 0;
        $user = Auth::guard('user')->user();

        $validator = Validator::make($request->all(), [
            's_latitude' => 'required|numeric',
            'd_latitude' => 'required|numeric',
            's_longitude' => 'required|numeric',
            'd_longitude' => 'required|numeric',
            'service_type' => 'required|numeric|exists:car_types,id',
            'name' => 'exists:promo_codes,name',
            'distance' => 'required|numeric',
            'use_wallet' => 'numeric',
            'payment_mode' => 'required|in:CASH,CARD,PAYPAL',
            'card_id' => ['required_if:payment_mode,CARD','exists:cards,card_id,user_id,'.$user->id],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
 
        $activeRequests = UserRequest::PendingRequest($user->id)->first();

        if($activeRequests) {
            return response()->json(['error' => trans('cabResponses.ride.request_inprogress')], 500);
        }

        if ( !empty($request->schedule_date)  && !empty($request->schedule_time) ) {
            $beforeschedule_time = (new Carbon("$request->schedule_date $request->schedule_time"))->subHour(1);
            $afterschedule_time = (new Carbon("$request->schedule_date $request->schedule_time"))->addHour(1);

            $checkScheduling = UserRequest::where('status','SCHEDULED')
                ->where('user_id', $user->id)
                ->whereBetween('schedule_at', [$beforeschedule_time, $afterschedule_time])
                ->first();
            if ($checkScheduling) {
                return response()->json(['error' => trans('cabResponses.ride.request_scheduled')], 500);
            }

        }

        $latitude = $request->s_latitude;
        $longitude = $request->s_longitude;
        $car_type = $request->service_type;

        $drivers = Driver::select(DB::Raw("(6371 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) AS distance"), 'id')
            // ->having('distance', '<=', $distance)
            ->where('status', 'APPROVED')
            ->whereHas('vehicles', function($query) use ($car_type) {
                $query->where('car_type_id', $car_type);
                $query->where('status', 'ACTIVE');
            })
            ->orderBy('distance','asc')
            ->take(5)
            ->get();

        if(!$drivers->count()) {
            return response()->json(['message' => 'No available drivers now']);
        }

        try {
            
            $userRequest = new UserRequest;
            $userRequest->booking_id = Str::random(6) . 'R' . $user->id;
            $userRequest->user_id = $user->id;
            $userRequest->car_type_id = $request->service_type;
            $userRequest->payment_mode = $request->payment_mode;
            $userRequest->status = 'SEARCHING';
            $userRequest->s_address = $request->s_address ? : "";
            $userRequest->d_address = $request->d_address ? : "";

            $userRequest->s_latitude = $request->s_latitude;
            $userRequest->s_longitude = $request->s_longitude;

            $userRequest->d_latitude = $request->d_latitude;
            $userRequest->d_longitude = $request->d_longitude;
            $userRequest->distance = $request->distance;
            $userRequest->is_track = true;
            $userRequest->assigned_at = Carbon::now();

            if($user->wallet_balance > 0) {
                $userRequest->use_wallet = $request->use_wallet ? : 0;
            }

            if ($request->has('route_key')) {
                $userRequest->route_key = $request->route_key;
            }

            if ($drivers->count() <= $surge_factor && $drivers->count() > 0) {
                $userRequest->surge = 1;
            }

            if( !empty($request->schedule_date)  && !empty($request->schedule_time) ){
                $userRequest->schedule_at = date("Y-m-d H:i:s", strtotime("$request->schedule_date $request->schedule_time"));
            }

            $userRequest->save();

            $user->update(['payment_mode' => $request->payment_mode]);

            if ($request->has('card_id')) {
                Card::where('user_id', $user->id)->update(['is_default' => 0]);
                Card::where('card_id', $request->card_id)->update(['is_default' => 1]);
            }

            $data = array(); 
            $arr = array();
            foreach($drivers as $driver) {
                $arr['request_id'] = $userRequest->id;
                $arr['driver_id'] = $driver->id;
                array_push($data, $arr);
            } 
            RequestFilter::insert($data);

            return response()->json([
                'message' => 'New request Created!',
                'request_id' => $userRequest->id
            ]);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function cancel_request(Request $request) {

        $this->validate($request, [
            'request_id' => 'required|numeric|exists:user_requests,id,user_id,'.Auth::guard('user')->user()->id,
        ]);

        try {

            $userRequest = UserRequest::findOrFail($request->request_id);

            if($userRequest->status == 'CANCELLED')
            {
                return response()->json(['error' => trans('cabResponses.ride.already_cancelled')], 500);
            }

            if(in_array($userRequest->status, ['SEARCHING','STARTED','ARRIVED','SCHEDULED'])) {

                if($userRequest->status != 'SEARCHING'){
                    $this->validate($request, [
                        'cancel_reason'=> 'max:255',
                    ]);
                }

                $userRequest->status = 'CANCELLED';
                $userRequest->cancel_reason = $request->cancel_reason;
                $userRequest->cancelled_by = 'USER';
                $userRequest->save();

                RequestFilter::where('request_id', $userRequest->id)->delete();

                if ($userRequest->driver_id && $userRequest->status != 'SCHEDULED') {
                    DriverVehicle::where('driver_id', $userRequest->driver_id)->update(['status' => 'ACTIVE']);
                }

                (new SendPushController)->UserCancellRide($userRequest);

                if($request->ajax()) {
                    return response()->json(['message' => trans('cabResponses.ride.ride_cancelled')]); 
                } else {
                    return redirect('dashboard')->with('flash_success','Request Cancelled Successfully');
                }

            } else {
                return response()->json(['error' => trans('cabResponses.ride.already_onride')], 500); 
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => trans('cabResponses.something_went_wrong')]);
        }

    }

    /**
     * Show the request status check.
     *
     * @return \Illuminate\Http\Response
     */

    public function request_status_check() {

        try {
            $user = Auth::guard('user')->user();
            $check_status = ['CANCELLED', 'SCHEDULED'];
            $userRequests = UserRequest::UserRequestStatusCheck($user->id, $check_status)
                ->get()
                ->toArray(); 

            $requests = UserRequest::UserRequestAssignProvider($user->id, 'SEARCHING')->get();  
            $timeout = 180;
            if($requests->count()) {
                foreach($requests as $request) {
                    $expiredTime = $timeout - (time() - strtotime($request->assigned_at));
                    if ($expiredTime < 0) {
                        $providerTrip = new TripController();
                        $providerTrip->assign_next_provider($request->id);
                    }
                }
            }

            return response()->json(['data' => $userRequests]);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


    public function rate_provider(Request $request) {

        $this->validate($request, [
            'request_id' => 'required|integer|exists:user_requests,id,user_id,'.Auth::guard('user')->user()->id,
            'rating' => 'required|integer|in:1,2,3,4,5',
            'comment' => 'max:255',
        ]);
    
        $userRequests = UserRequest::where('id', $request->request_id)
            ->where('status', 'COMPLETED')
            ->where('paid', 0)
            ->first();

        if ($userRequests) {
            return response()->json(['error' => trans('cabResponses.user.not_paid')], 500);
        }

        try {
            $userRequest = UserRequest::findOrFail($request->request_id);

            if ($userRequest->rating == null) {
                UserRequestRating::create([
                    'driver_id' => $userRequest->driver_id,
                    'user_id' => $userRequest->user_id,
                    'request_id' => $userRequest->id,
                    'user_rating' => $request->rating,
                    'user_comment' => $request->comment,
                ]);
            } else {
                $userRequest->rating->update([
                    'user_rating' => $request->rating,
                    'user_comment' => $request->comment,
                ]);
            }

            $userRequest->user_rated = 1;
            $userRequest->save();

            $average = UserRequestRating::where('driver_id', $userRequest->driver_id)->avg('user_rating');

            Driver::where('id', $userRequest->driver_id)->update(['rating' => $average]);

            // Send Push Notification to Driver 
            return response()->json(['message' => trans('cabResponses.ride.driver_rated')]);
        } catch (Exception $e) {
            return response()->json(['error' => trans('cabResponses.something_went_wrong')], 500);
        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


    public function modifiy_request(Request $request) {

        $this->validate($request, [
            'request_id' => 'required|integer|exists:user_requests,id,user_id,'.Auth::guard('user')->user()->id,
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'required'
        ]);

        try{

            $userRequest = UserRequest::findOrFail($request->request_id);
            $userRequest->d_latitude = $request->latitude ?: $userRequest->d_latitude;
            $userRequest->d_longitude = $request->longitude ?: $userRequest->d_longitude;
            $userRequest->d_address =  $request->address ?: $userRequest->d_address;
            $userRequest->save();

            // Send Push Notification to Driver 
            return response()->json(['message' => trans('cabResponses.ride.request_modify_location')]); 

        } catch (Exception $e) {
            return response()->json(['error' => trans('cabResponses.something_went_wrong')], 500);
        }

    } 


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function trips() {
    
        try{
            $userRequests = UserRequest::UserTrips(Auth::guard('user')->user()->id)->get();
            if (!empty($userRequests)) {
                $marker = '/assets/icons/marker.png';
                foreach ($userRequests as $key => $value) {
                    $userRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
                    "autoscale=1".
                    "&size=320x130".
                    "&maptype=terrian".
                    "&format=png".
                    "&visual_refresh=true".
                    "&markers=icon:".$marker."%7C".$value->s_latitude.",".$value->s_longitude.
                    "&markers=icon:".$marker."%7C".$value->d_latitude.",".$value->d_longitude.
                    "&path=color:0x191919|weight:3|enc:".$value->route_key.
                    "&key=".env('GOOGLE_MAP_KEY', null);
                }
            }
            return $userRequests;
        }
        catch (Exception $e) {
            return response()->json(['error' => trans('cabResponses.something_went_wrong')]);
        }
    }


    public function estimated_fare(Request $request)
    {
        $this->validate($request,[
            's_latitude' => 'required|numeric',
            's_longitude' => 'required|numeric',
            'd_latitude' => 'required|numeric',
            'd_longitude' => 'required|numeric',
            'service_type' => 'required|numeric',
        ]);

        try {

            $details = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$request->s_latitude.",".$request->s_longitude."&destinations=".$request->d_latitude.",".$request->d_longitude."&mode=driving&sensor=false&key=".env('GOOGLE_MAP_KEY', null);

            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $details );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            $result = curl_exec( $ch );
            curl_close( $ch );

            $details = json_decode($result, TRUE);

            $meter = $details['rows'][0]['elements'][0]['distance']['value'];
            $time = $details['rows'][0]['elements'][0]['duration']['text'];
            $seconds = $details['rows'][0]['elements'][0]['duration']['value'];

            $kilometer = round($meter/1000);
            $minutes = round($seconds/60);

            // Settings
            $tax_percentage = 14;
            $driver_search_radius = 2000;
            $surge_trigger = 0;
            $surge_percentage = 0;

            $car_type = CarType::findOrFail($request->service_type);
            $base_price = $car_type->fixed;
            $price = $car_type->fixed;

            if ($car_type->calculator == 'MIN') {
                $price += $car_type->minute * $minutes;
            } else if ($car_type->calculator == 'HOUR') {
                $price += $car_type->minute * 60;
            } else if ($car_type->calculator == 'DISTANCE') {
                $price += ($kilometer * $car_type->price);
            } else if ($car_type->calculator == 'DISTANCEMIN') {
                $price += ($kilometer * $car_type->price) + ($car_type->minute * $minutes);
            } else if ($car_type->calculator == 'DISTANCEHOUR') {
                $price += ($kilometer * $car_type->price) + ($car_type->minute * $minutes * 60);
            } else {
                $price += ($kilometer * $car_type->price);
            }

            $tax_price = ( $tax_percentage/100 ) * $price;
            $total = $price + $tax_price;
            $car_type = $request->service_type;
            $latitude = $request->s_latitude;
            $longitude = $request->s_longitude;

            $drivers = Driver::where('status', 'APPROVED')
                ->whereRaw("(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $driver_search_radius")
                ->whereHas('vehicles', function($query) use ($car_type) { 
                    $query->where('car_type_id', $car_type); 
                    $query->where('status','ACTIVE');
                })
                ->get();

            $surge = 0;
            
            if($drivers->count() <= $surge_trigger && $drivers->count() > 0){
                $surge_price = ($surge_percentage/100) * $total;
                $total += $surge_price;
                $surge = 1;
            }

            $surge_percentage = 1+($surge_percentage/100)."X";

            return response()->json([
                'estimated_fare' => round($total,2), 
                'distance' => $kilometer,
                'time' => $time,
                'surge' => $surge,
                'surge_value' => $surge_percentage,
                'tax_price' => $tax_price,
                'base_price' => $base_price,
            ]);

        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function trip_details(Request $request) {

         $this->validate($request, [
            'request_id' => 'required|integer|exists:user_requests,id',
        ]);
    
        try {
            $userRequests = UserRequest::UserTripDetails(Auth::guard('user')->user()->id, $request->request_id)->get();
            if (!empty($userRequests)) {
                $marker = '/assets/icons/marker.png';
                foreach ($userRequests as $key => $value) {
                    $userRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
                    "autoscale=1".
                    "&size=320x130".
                    "&maptype=terrian".
                    "&format=png".
                    "&visual_refresh=true".
                    "&markers=icon:".$marker."%7C".$value->s_latitude.",".$value->s_longitude.
                    "&markers=icon:".$marker."%7C".$value->d_latitude.",".$value->d_longitude.
                    "&path=color:0x191919|weight:3|enc:".$value->route_key.
                    "&key=".env('GOOGLE_MAP_KEY', null);
                }
            }
            return $userRequests;
        }

        catch (Exception $e) {
            return response()->json(['error' => trans('cabResponses.something_went_wrong')]);
        }
    }

    /**
     * get all promo code.
     *
     * @return \Illuminate\Http\Response
     */

    public function promocodes() {
        try {
            $this->check_expiry();

            return PromoCodeUsage::Active()
                ->where('user_id', Auth::guard('user')->user()->id)
                ->with('promocode')
                ->get();

        } catch (Exception $e) {
            return response()->json(['error' => trans('cabResponses.something_went_wrong')], 500);
        }
    } 


    public function check_expiry(){
        try{
            $PromoCode = PromoCode::all();
            foreach ($PromoCode as $index => $promo) {
                if(date("Y-m-d") > $promo->expires_on) {
                    PromoCodeUsage::where('promo_code_id', $promo->id)->update(['status' => 'EXPIRED']);
                } else {
                    PromoCodeUsage::where('promo_code_id', $promo->id)
                        ->where('status','<>','USED')
                        ->update(['status' => 'ADDED']);
                }
            }
        } catch (Exception $e) {
            return response()->json(['error' => trans('cabResponses.something_went_wrong')], 500);
        }
    }


    /**
     * add promo code.
     *
     * @return \Illuminate\Http\Response
     */

    public function add_promocode(Request $request) {

        $this->validate($request, [
            'promocode' => 'required|exists:promo_codes,name',
        ]);

        try{

            $find_promo = PromoCode::where('name', $request->promocode)->first();

            if (date("Y-m-d") > $find_promo->expires_on) {
                return response()->json([
                    'message' => trans('cabResponses.promocode_expired'), 
                    'code' => 'promocode_expired'
                ]);

            } else if (PromoCodeUsage::where('promo_code_id', $find_promo->id)->where('user_id', Auth::guard('user')->user()->id)->whereIn('status', ['ADDED','USED'])->count() > 0){
                return response()->json([
                    'message' => trans('cabResponses.promocode_already_in_use'), 
                    'code' => 'promocode_already_in_use'
                ]);
            } else {
                $promo = new PromoCodeUsage;
                $promo->promo_code_id = $find_promo->id;
                $promo->user_id = Auth::guard('user')->user()->id;
                $promo->status = 'ADDED';
                $promo->save();
                return response()->json([
                    'message' => trans('cabResponses.promocode_applied') ,
                    'code' => 'promocode_applied'
                ]); 
            }
        } catch (Exception $e) {
            return response()->json(['error' => trans('cabResponses.something_went_wrong')], 500);
        }
    } 

    public function upcoming_trips() {
    
        try {
            $userRequests = UserRequest::UserUpcomingTrips(Auth::guard('user')->user()->id)->get();
            if (!empty($userRequests)) {
                $marker = '/assets/icons/marker.png';
                foreach ($userRequests as $key => $value) {
                    $userRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
                    "autoscale=1".
                    "&size=320x130".
                    "&maptype=terrian".
                    "&format=png".
                    "&visual_refresh=true".
                    "&markers=icon:".$marker."%7C".$value->s_latitude.",".$value->s_longitude.
                    "&markers=icon:".$marker."%7C".$value->d_latitude.",".$value->d_longitude.
                    "&path=color:0x000000|weight:3|enc:".$value->route_key.
                    "&key=".env('GOOGLE_MAP_KEY', null);
                }
            }
            return $userRequests;
        }

        catch (Exception $e) {
            return response()->json(['error' => trans('cabResponses.something_went_wrong')]);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function upcoming_trip_details(Request $request) {

         $this->validate($request, [
            'request_id' => 'required|integer|exists:user_requests,id',
        ]);
    
        try{
            $userRequests = UserRequest::UserUpcomingTripDetails(Auth::guard('user')->user()->id,$request->request_id)->get();
            if (!empty($userRequests)) {
                $marker = '/assets/icons/marker.png';
                foreach ($userRequests as $key => $value) {
                    $userRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
                    "autoscale=1".
                    "&size=320x130".
                    "&maptype=terrian".
                    "&format=png".
                    "&visual_refresh=true".
                    "&markers=icon:".$marker."%7C".$value->s_latitude.",".$value->s_longitude.
                    "&markers=icon:".$marker."%7C".$value->d_latitude.",".$value->d_longitude.
                    "&path=color:0x000000|weight:3|enc:".$value->route_key.
                    "&key=".env('GOOGLE_MAP_KEY', null);
                }
            }
            return $userRequests;
        }

        catch (Exception $e) {
            return response()->json(['error' => trans('cabResponses.something_went_wrong')]);
        }
    }


    /**
     * Show the nearby drivers.
     *
     * @return \Illuminate\Http\Response
     */

    public function show_providers(Request $request) {

        $this->validate($request, [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'service_type' => 'numeric|exists:car_types,id',
        ]);

        try{

            $distance = env('PROVIDER_SEARCH_RADIUS', 10);
            $latitude = $request->latitude;
            $longitude = $request->longitude;

            if ($request->has('car_type')) {
                $car_type = $request->service_type;

                $drivers = Driver::where('status', 'APPROVED')
                    ->whereRaw("(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
                    ->whereHas('vehicles', function($query) use ($car_type) { 
                        $query->where('car_type_id', $car_type); 
                        $query->where('status', 'ACTIVE');
                    })
                    ->get();

            } else {

                $drivers = Driver::where('status', 'APPROVED')
                    ->whereRaw("(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
                    ->whereHas('vehicle', function ($query) {
                        $query->where('status','ACTIVE');
                    })
                    ->get();
            }
            return $drivers;

        } catch (Exception $e) {
            return response()->json(['error' => trans('cabResponses.something_went_wrong')], 500);
        }
    }


    public function forgot_password(Request $request){

        $this->validate($request, [
            'email' => 'required|email|exists:users,email',
        ]);

        try {  
            
            $user = User::where('email' , $request->email)->first();
            $otp = mt_rand(100000, 999999);
            $user->otp = $otp;
            $user->save();

            return response()->json([
                'message' => 'OTP sent to your email!',
                'user' => $user
            ]);

        } catch(Exception $e) {
            return response()->json(['error' => trans('cabResponses.something_went_wrong')], 500);
        }
    }


    public function reset_password(Request $request){

        $this->validate($request, [
            'password' => 'required|confirmed|min:6',
            'id' => 'required|numeric|exists:users,id'

        ]);

        try {
            $User = User::findOrFail($request->id);
            $User->password = bcrypt($request->password);
            $User->save();
            if($request->ajax()) {
                return response()->json(['message' => 'Password Updated']);
            }
        } catch (Exception $e) {
            if($request->ajax()) {
                return response()->json(['error' => trans('cabResponses.something_went_wrong')]);
            }
        }
    }

    public function help_details(Request $request)
    {
        return response()->json([
            'contact_number' => '123', 
            'contact_email' => 'support@qruz.app'
        ]);
    }


    public function verify(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:255|unique:users',
        ]);

        try {
            
            return response()->json(['message' => trans('cabResponses.email_available')]);

        } catch (Exception $e) {
             return response()->json(['error' => trans('cabResponses.something_went_wrong')], 500);
        }
    }



    /**
     * Show the wallet usage.
     *
     * @return \Illuminate\Http\Response
     */

    public function wallet_passbook(Request $request)
    {
        try{
            
            return WalletPassbook::where('user_id',Auth::guard('user')->user()->id)->get();

        } catch (Exception $e) {
             return response()->json(['error' => trans('cabResponses.something_went_wrong')], 500);
        }
    }


    /**
     * Show the promo usage.
     *
     * @return \Illuminate\Http\Response
     */

    public function promo_passbook(Request $request)
    {
        try {
            return PromoCodeUsage::where('user_id',Auth::guard('user')->user()->id)->with('promocode')->get();
        } catch (Exception $e) {
            return response()->json(['error' => trans('cabResponses.something_went_wrong')], 500);
        }
    }

}
