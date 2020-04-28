<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Auth;
use Carbon\Carbon;
use App\Http\Controllers\SendPushController;

use App\User;
use App\PromoCode;
use App\UserRequest;
use App\RequestFilter;
use App\PromoCodeUsage;
use App\DriverVehicle;
use App\UserRequestRating;
use App\UserRequestPayment;
use App\CarType;
use App\WalletPassbook;
use Location\Coordinate;
use Location\Distance\Vincenty;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $driver = Auth::guard('driver')->user();

            $driver_id = $driver->id;

            $AfterAssignProvider = RequestFilter::with(['request.user', 'request.payment', 'request'])
                ->where('driver_id', $driver_id)
                ->whereHas('request', function($query) use ($driver_id) {
                    $query->where('status','<>', 'CANCELLED');
                    $query->where('status','<>', 'SCHEDULED');
                    $query->where('driver_id', $driver_id );
                    $query->where('current_driver_id', $driver_id);
                });

            if(env('BROADCAST_REQUEST', 0) == 1){
                $BeforeAssignProvider = RequestFilter::with(['request.user', 'request.payment', 'request'])
                ->where('driver_id', $driver_id)
                ->whereHas('request', function($query) use ($driver_id){
                    $query->where('status','<>', 'CANCELLED');
                    $query->where('status','<>', 'SCHEDULED');
                    $query->whereNull('current_driver_id');
                });
            }else{
                $BeforeAssignProvider = RequestFilter::with(['request.user', 'request.payment', 'request'])
                ->where('driver_id', $driver_id)
                ->whereHas('request', function($query) use ($driver_id){
                    $query->where('status','<>', 'CANCELLED');
                    $query->where('status','<>', 'SCHEDULED');
                    $query->where('current_driver_id',$driver_id);
                });    
            }
                
            $IncomingRequests = $BeforeAssignProvider->union($AfterAssignProvider)->get();

            if(!empty($request->latitude)) {
                $driver->update([
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ]);
            }

            if(env('MANUAL_REQUEST', 0) == 0) {
                $Timeout = env('DRIVER_SELECT_TIMEOUT', '180');
                if(!empty($IncomingRequests)){
                    for ($i=0; $i < sizeof($IncomingRequests); $i++) {
                        $IncomingRequests[$i]->time_left_to_respond = $Timeout - (time() - strtotime($IncomingRequests[$i]->request->assigned_at));
                        if($IncomingRequests[$i]->request->status == 'SEARCHING' && $IncomingRequests[$i]->time_left_to_respond < 0) {
                            if(env('BROADCAST_REQUEST', 0) == 1){
                                $this->assign_destroy($IncomingRequests[$i]->request->id);
                            }else{
                                $this->assign_next_provider($IncomingRequests[$i]->request->id);
                            }
                        }
                    }
                }

            }

            $Response = [
                'account_status' => $driver->status,
                'service_status' => $driver->vehicle ? Auth::guard('driver')->vehicle->status : 'OFFLINE',
                'requests' => $IncomingRequests,
            ];

            return $Response;
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Something went wrong']);
        }
    }

    /**
     * Calculate distance between two coordinates.
     * 
     * @return \Illuminate\Http\Response
     */

    public function calculate_distance(Request $request, $id)
    {
        $this->validate($request, [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        try {
            $driver = Auth::guard('driver')->user();

            $UserRequest = UserRequest::where('status','PICKEDUP')
                ->where('driver_id',$driver->id)
                ->find($id);

            if ($UserRequest && ($request->latitude && $request->longitude)) {
            
                if ($UserRequest->track_latitude && $UserRequest->track_longitude) {

                    $coordinate1 = new Coordinate($UserRequest->track_latitude, $UserRequest->track_longitude); 
                    $coordinate2 = new Coordinate($request->latitude, $request->longitude); 

                    $calculator = new Vincenty();

                    $mydistance = $calculator->getDistance($coordinate1, $coordinate2); 

                    $meters = round($mydistance);

                    if ($meters >= 100) {
                        $traveldistance = round(($meters/1000),8);

                        $calulatedistance = $UserRequest->track_distance + $traveldistance;

                        $UserRequest->track_distance  = $calulatedistance;
                        $UserRequest->distance        = $calulatedistance;
                        $UserRequest->track_latitude  = $request->latitude;
                        $UserRequest->track_longitude = $request->longitude;
                        $UserRequest->save();
                    }
                } else if (!$UserRequest->track_latitude && !$UserRequest->track_longitude) {
                    $UserRequest->distance             = 0;
                    $UserRequest->track_latitude      = $request->latitude;
                    $UserRequest->track_longitude     = $request->longitude;
                    $UserRequest->save();
                }
            }
            return $UserRequest;
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Something went wrong']);
        }
    }

    /**
     * Cancel given request.
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request)
    {
        $this->validate($request, [
            'cancel_reason'=> 'max:255',
        ]);
        
        try {

            $UserRequest = UserRequest::findOrFail($request->id);
            $Cancellable = ['SEARCHING', 'ACCEPTED', 'ARRIVED', 'STARTED', 'CREATED','SCHEDULED'];

            if(!in_array($UserRequest->status, $Cancellable)) {
                return response()->json(['error' => 'Cannot cancel request at this stage!']);
            }

            $UserRequest->status = "CANCELLED";
            $UserRequest->cancel_reason = $request->cancel_reason;
            $UserRequest->cancelled_by = "DRIVER";
            $UserRequest->save();

             RequestFilter::where('request_id', $UserRequest->id)->delete();

             DriverVehicle::where('driver_id',$UserRequest->driver_id)->update(['status' =>'ACTIVE']);

             // Send Push Notification to User
            (new SendPushController)->ProviderCancellRide($UserRequest);

            return $UserRequest;

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Something went wrong']);
        }


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function rate(Request $request, $id)
    {

        $this->validate($request, [
                'rating' => 'required|integer|in:1,2,3,4,5',
                'comment' => 'max:255',
            ]);
    
        try {

            $UserRequest = UserRequest::where('id', $id)
                ->where('status', 'COMPLETED')
                ->firstOrFail();

            if($UserRequest->rating == null) {
                UserRequestRating::create([
                        'driver_id' => $UserRequest->driver_id,
                        'user_id' => $UserRequest->user_id,
                        'request_id' => $UserRequest->id,
                        'driver_rating' => $request->rating,
                        'driver_comment' => $request->comment,
                    ]);
            } else {
                $UserRequest->rating->update([
                        'driver_rating' => $request->rating,
                        'driver_comment' => $request->comment,
                    ]);
            }

            $UserRequest->update(['driver_rated' => 1]);

            // Delete from filter so that it doesn't show up in status checks.
            RequestFilter::where('request_id', $id)->delete();

            DriverVehicle::where('driver_id',$UserRequest->driver_id)->update(['status' =>'ACTIVE']);

            // Send Push Notification to Driver 
            $average = UserRequestRating::where('driver_id', $UserRequest->driver_id)->avg('driver_rating');

            $UserRequest->user->update(['rating' => $average]);

            return response()->json(['message' => 'Request Completed!']);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Request not yet completed!'], 500);
        }
    }

    /**
     * Get the trip history of the driver
     *
     * @return \Illuminate\Http\Response
     */
    public function scheduled(Request $request)
    {
        
        try{

            $Jobs = UserRequest::where('driver_id', Auth::guard('driver')->user()->id)
                ->where('status', 'SCHEDULED')
                ->with('car_type')
                ->get();

            if(!empty($Jobs)){
                $marker = '/assets/icons/marker.png';
                foreach ($Jobs as $key => $value) {
                    $Jobs[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
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

            return $Jobs;
            
        } catch(Exception $e) {
            return response()->json(['error' => "Something Went Wrong"]);
        }
    }

    /**
     * Get the trip history of the driver
     *
     * @return \Illuminate\Http\Response
     */
    public function history(Request $request)
    {
        $Jobs = UserRequest::where('driver_id', Auth::guard('driver')->user()->id)
                ->where('status', 'COMPLETED')
                ->orderBy('created_at','desc')
                ->with('payment')
                ->get();

        if(!empty($Jobs)){
            $marker = 'asset/marker.png';
            foreach ($Jobs as $key => $value) {
                $Jobs[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
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
        return $Jobs;
    }

    public function accept(Request $request, $id)
    {
        try {

            $UserRequest = UserRequest::findOrFail($id);

            if ($UserRequest->status != "SEARCHING") {
                return response()->json(['error' => 'Request already under progress!']);
            }

            $driver_id = Auth::guard('driver')->user()->id;
            
            $UserRequest->driver_id = $driver_id;

            if(env('BROADCAST_REQUEST', 0) == 1){
               $UserRequest->current_driver_id = $driver_id; 
            }

            if($UserRequest->schedule_at){

                $beforeschedule_time = strtotime($UserRequest->schedule_at."- 1 hour");
                $afterschedule_time = strtotime($UserRequest->schedule_at."+ 1 hour");

                $CheckScheduling = UserRequest::where('status','SCHEDULED')
                    ->where('driver_id', $driver_id)
                    ->whereBetween('schedule_at',[$beforeschedule_time,$afterschedule_time])
                    ->count();

                if ($CheckScheduling > 0 ) {
                    return response()->json(['error' => trans('cabResponses.ride.request_already_scheduled')]);
                }

                RequestFilter::where('request_id',$UserRequest->id)->where('driver_id',$driver_id)->update(['status' => 2]);

                $UserRequest->status = "SCHEDULED";
                $UserRequest->save();

            } else {

                $UserRequest->status = "STARTED";
                $UserRequest->save();

                DriverVehicle::where('driver_id',$UserRequest->driver_id)->update(['status' =>'RIDING']);

                $Filters = RequestFilter::where('request_id', $UserRequest->id)->where('driver_id', '!=', $driver_id)->delete();
            }

            RequestFilter::where('request_id','!=' ,$UserRequest->id)
                ->where('driver_id',$driver_id )
                ->whereHas('request', function($query){
                    $query->where('status','<>','SCHEDULED');
                })
                ->delete(); 

            // Send Push Notification to User
            (new SendPushController)->RideAccepted($UserRequest);

            return $UserRequest->with('user')->get();

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Unable to accept, Please try again later']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Connection Error']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required|in:ACCEPTED,STARTED,ARRIVED,PICKEDUP,DROPPED,PAYMENT,COMPLETED',
        ]);

        try {

            $UserRequest = UserRequest::with('user')->findOrFail($id);

            if($request->status == 'DROPPED' && $UserRequest->payment_mode != 'CASH') {
                $UserRequest->status = 'COMPLETED';
            } else if ($request->status == 'COMPLETED' && $UserRequest->payment_mode == 'CASH') {
                $UserRequest->status = $request->status;
                $UserRequest->paid = 1;
                // DriverVehicle::where('driver_id',$UserRequest->driver_id)->update(['status' =>'ACTIVE']);
            } else {
                $UserRequest->status = $request->status;

                if($request->status == 'ARRIVED'){
                    (new SendPushController)->Arrived($UserRequest);
                }
            }

            if($request->status == 'PICKEDUP'){
                if($UserRequest->is_track){
                   $UserRequest->distance = 0; 
                }
                $UserRequest->started_at = Carbon::now();
            }

            $UserRequest->save();

            if($request->status == 'DROPPED') {
                if($UserRequest->is_track){
                    $UserRequest->d_latitude = $request->latitude?:$UserRequest->d_latitude;
                    $UserRequest->d_longitude = $request->longitude?:$UserRequest->d_longitude;
                    $UserRequest->d_address =  $request->address?:$UserRequest->d_address;
                }
                $UserRequest->finished_at = Carbon::now();
                $StartedDate  = date_create($UserRequest->started_at);
                $FinisedDate  = Carbon::now();
                $TimeInterval = date_diff($StartedDate,$FinisedDate);
                $MintuesTime  = $TimeInterval->i;
                $UserRequest->travel_time = $MintuesTime;
                $UserRequest->save();
                $UserRequest->with('user')->findOrFail($id);
                $UserRequest->invoice = $this->invoice($id);

                (new SendPushController)->Dropped($UserRequest);
            }

           
            // Send Push Notification to User
       
            return $UserRequest;

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Unable to update, Please try again later']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Connection Error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $UserRequest = UserRequest::find($id);

        try {
            $this->assign_next_provider($UserRequest->id);
            return $UserRequest->with('user')->get();

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Unable to reject, Please try again later']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Connection Error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function assign_destroy($id)
    {
        $UserRequest = UserRequest::find($id);
        try {
            UserRequest::where('id', $UserRequest->id)->update(['status' => 'CANCELLED']);
            // No longer need request specific rows from RequestMeta
            RequestFilter::where('request_id', $UserRequest->id)->delete();
            //  request push to user driver not available
            (new SendPushController)->ProviderNotAvailable($UserRequest->user_id);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Unable to reject, Please try again later']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Connection Error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function assign_next_provider($request_id) {

        try {
            $UserRequest = UserRequest::findOrFail($request_id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Request not found'], 404);
        }

        $RequestFilter = RequestFilter::where('driver_id', $UserRequest->current_driver_id)
            ->where('request_id', $UserRequest->id)
            ->delete();

        try {
            $next_provider = RequestFilter::where('request_id', $UserRequest->id)
                ->orderBy('id')
                ->firstOrFail();

            $UserRequest->current_driver_id = $next_provider->driver_id;
            $UserRequest->assigned_at = Carbon::now();
            $UserRequest->save();

            (new SendPushController)->IncomingRequest($next_provider->driver_id);
            
        } catch (ModelNotFoundException $e) {
            UserRequest::where('id', $UserRequest->id)->update(['status' => 'CANCELLED']);
            (new SendPushController)->ProviderNotAvailable($UserRequest->user_id);
        }
    }

    public function invoice($request_id)
    {
        try {
            $UserRequest = UserRequest::findOrFail($request_id);
            $tax_percentage = env('TAX_PERCENTAGE', 14);
            $commission_percentage = env('COMMISSION_PERCENTAGE', 10);
            $provider_commission_percentage = env('PROVIDER_COMMISSION_PERCENTAGE', 10);
            $car_type = CarType::findOrFail($UserRequest->car_type_id);
            
            $kilometer = $UserRequest->distance;
            $Fixed = $car_type->fixed;
            $Distance = 0;
            $minutes = 0;
            $Discount = 0; // Promo Code discounts should be added here.
            $Wallet = 0;
            $Surge = 0;
            $ProviderCommission = 0;
            $ProviderPay = 0;

            if($car_type->calculator == 'MIN') {
                $Distance = $car_type->minute * $minutes;
            } else if($car_type->calculator == 'HOUR') {
                $Distance = $car_type->minute * 60;
            } else if($car_type->calculator == 'DISTANCE') {
                $Distance = ($kilometer * $car_type->price);
            } else if($car_type->calculator == 'DISTANCEMIN') {
                $Distance = ($kilometer * $car_type->price) + ($car_type->minute * $minutes);
            } else if($car_type->calculator == 'DISTANCEHOUR') {
                $Distance = ($kilometer * $car_type->price) + ($car_type->minute * $minutes * 60);
            } else {
                $Distance = ($kilometer * $car_type->price);
            }

             $commission = ($Distance + $Fixed) * ( $commission_percentage/100 );
             $Tax = ($Distance + $Fixed) * ( $tax_percentage/100 );
             $ProviderCommission = ($Distance + $Fixed) * ( $provider_commission_percentage/100 );
             $ProviderPay = ($Distance + $Fixed) - $ProviderCommission;

            if($PromoCodeUsage = PromoCodeUsage::where('user_id', $UserRequest->user_id)->where('status','ADDED')->first())
            {
                if($PromoCode = PromoCode::find($PromoCodeUsage->promo_code_id)){
                    $Discount = $PromoCode->discount;
                    $PromoCodeUsage->status ='USED';
                    $PromoCodeUsage->save();
                }

                if ($PromoCodeUsage->promocode->discount_type=='AMOUNT') {
                    $Total = $Fixed + $Distance + $Tax - $Discount;
                } else {
                    $Total = ($Fixed + $Distance + $Tax)-(($Fixed + $Distance + $Tax) * ($Discount/100));
                    $Discount = (($Fixed + $Distance + $Tax) * ($Discount/100));
                }

            } else {
                
                $Total = $Fixed + $Distance + $Tax - $Discount;
            }

            
            if ($UserRequest->surge) {
                $Surge = (env('SURGE_PERCENTAGE', 0)/100) * $Total;
                $Total += $Surge;
            }

            if ($Total < 0) {
                $Total = 0.00; // prevent from negative value
            }

            $Payment = new UserRequestPayment;
            $Payment->request_id = $UserRequest->id;

            /*
            * Reported by Jeya, We are adding the surge price with Base price of Service Type.
            */ 
            $Payment->fixed = $Fixed + $Surge;
            $Payment->distance = $Distance;
            $Payment->commission = $commission;
            $Payment->surge = $Surge;
            $Payment->total = $Total;
            $Payment->driver_commission = $ProviderCommission;
            $Payment->driver_pay = $ProviderPay;
            if($Discount != 0 && $PromoCodeUsage){
                $Payment->promo_code_id = $PromoCodeUsage->promo_code_id;
            }
            $Payment->discount = $Discount;

            if($Discount  == ($Fixed + $Distance + $Tax)){
                $UserRequest->paid = 1;
            }

            if($UserRequest->use_wallet == 1 && $Total > 0){

                $User = User::find($UserRequest->user_id);

                $Wallet = $User->wallet_balance;

                if($Wallet != 0){

                    if($Total > $Wallet) {

                        $Payment->wallet = $Wallet;
                        $Payable = $Total - $Wallet;
                        User::where('id',$UserRequest->user_id)->update(['wallet_balance' => 0 ]);
                        $Payment->payable = abs($Payable);

                        WalletPassbook::create([
                          'user_id' => $UserRequest->user_id,
                          'amount' => $Wallet,
                          'status' => 'DEBITED',
                          'via' => 'TRIP',
                        ]);

                        // charged wallet money push 
                        (new SendPushController)->ChargedWalletMoney($UserRequest->user_id,currency($Wallet));

                    } else {

                        $Payment->payable = 0;
                        $WalletBalance = $Wallet - $Total;
                        User::where('id',$UserRequest->user_id)->update(['wallet_balance' => $WalletBalance]);
                        $Payment->wallet = $Total;
                        
                        $Payment->payment_id = 'WALLET';
                        $Payment->payment_mode = $UserRequest->payment_mode;

                        $UserRequest->paid = 1;
                        $UserRequest->status = 'COMPLETED';
                        $UserRequest->save();

                        WalletPassbook::create([
                          'user_id' => $UserRequest->user_id,
                          'amount' => $Total,
                          'status' => 'DEBITED',
                          'via' => 'TRIP',
                        ]);

                        // charged wallet money push 
                        (new SendPushController)->ChargedWalletMoney($UserRequest->user_id, currency($Total));
                    }

                }

            } else {
                $Payment->total = abs($Total);
                $Payment->payable = abs($Total);
                
            }

            $Payment->tax = $Tax;
            $Payment->save();

            return $Payment;

        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    /**
     * Get the trip history details of the driver
     *
     * @return \Illuminate\Http\Response
     */
    public function history_details(Request $request)
    {
        $this->validate($request, [
            'request_id' => 'required|integer|exists:user_requests,id',
        ]);

        if($request->ajax()) {
            
            $Jobs = UserRequest::where('id',$request->request_id)
                ->where('driver_id', Auth::guard('driver')->user()->id)
                ->with('payment','car_type','user','rating')
                ->get();
            if(!empty($Jobs)){
                $marker = '/assets/icons/marker.png';
                foreach ($Jobs as $key => $value) {
                    $Jobs[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
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

            return $Jobs;
        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function upcoming_trips() {
    
        try{
            $UserRequest = UserRequest::ProviderUpcomingRequest(Auth::guard('driver')->user()->id)->get();
            if(!empty($UserRequest)){
                $marker = 'asset/marker.png';
                foreach ($UserRequest as $key => $value) {
                    $UserRequest[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
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
            return $UserRequest;
        }

        catch (Exception $e) {
            return response()->json(['error' => trans('cabResponses.something_went_wrong')]);
        }
    }

    /**
     * Get the trip history details of the driver
     *
     * @return \Illuminate\Http\Response
     */
    public function upcoming_details(Request $request)
    {
        $this->validate($request, [
            'request_id' => 'required|integer|exists:user_requests,id',
        ]);

        if($request->ajax()) {
            
            $Jobs = UserRequest::where('id',$request->request_id)
                ->where('driver_id', Auth::guard('driver')->user()->id)
                ->with('car_type','user')
                ->get();
            if(!empty($Jobs)){
                $marker = '/assets/icons/marker.png';
                foreach ($Jobs as $key => $value) {
                    $Jobs[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
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
            return $Jobs;
        }

    }

    /**
     * Get the trip history details of the driver
     *
     * @return \Illuminate\Http\Response
     */
    public function summary(Request $request)
    {
        try{
            if($request->ajax()) {
                $rides = UserRequest::where('driver_id', Auth::guard('driver')->user()->id)->count();
                $revenue = UserRequestPayment::whereHas('request', function($query) use ($request) {
                    $query->where('driver_id', Auth::guard('driver')->user()->id);
                })
                ->sum('total');
                $cancel_rides = UserRequest::where('status','CANCELLED')->where('driver_id', Auth::guard('driver')->user()->id)->count();
                $scheduled_rides = UserRequest::where('status','SCHEDULED')->where('driver_id', Auth::guard('driver')->user()->id)->count();

                return response()->json([
                    'rides' => $rides, 
                    'revenue' => $revenue,
                    'cancel_rides' => $cancel_rides,
                    'scheduled_rides' => $scheduled_rides,
                ]);
            }

        } catch (Exception $e) {
            return response()->json(['error' => trans('cabResponses.something_went_wrong')]);
        }

    }

    /**
     * help Details.
     *
     * @return \Illuminate\Http\Response
     */

    public function help_details(Request $request){

        try {
            return response()->json([
                'contact_number' => '012345678', 
                'contact_email' => 'support@qruz.app'
            ]);

        } catch (Exception $e) {
            return response()->json(['error' => trans('cabResponses.something_went_wrong')]);
        }
    }

}
