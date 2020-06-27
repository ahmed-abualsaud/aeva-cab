<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\User;
use App\PromoCode;
use App\CabRequest;
use App\CabRequestFilter;
use App\PromoCodeUsage;
use App\DriverVehicle;
use App\Rating;
use App\CabRequestPayment;
use App\CarType;
use App\WalletPassbook;
use App\Helpers\StaticMapUrl;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SendPushController;
use Carbon\Carbon;
use Location\Coordinate;
use Location\Distance\Vincenty;

class TripController extends Controller
{
    public $tax_percentage = 14;
    public $commission_percentage = 0;
    public $driver_commission_percentage = 0;
    public $surge_percentage = 0;

    public function index(Request $request)
    {
        try {

            $driver = auth('driver')->user();

            $incomingRequests = CabRequest::with('user', 'payment')
                ->join('cab_request_filters', 'cab_request_filters.request_id', 'cab_requests.id')
                ->where('cab_request_filters.driver_id', $driver->id)
                ->whereNotIn('cab_requests.status', ['CANCELLED', 'SCHEDULED'])
                ->selectRaw("cab_requests.*, 180 - (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(CONVERT_TZ(cab_requests.assigned_at, 'Africa/Cairo', 'SYSTEM'))) AS time_left_to_respond");
            
            $latestRequest = [];
            
            if ($incomingRequests->count()) {

                $latestRequest = $incomingRequests->latest('cab_requests.created_at')
                    ->take(1)
                    ->get();

                $expiredRequests = $incomingRequests->where('cab_requests.status', 'SEARCHING')
                    ->having('time_left_to_respond', '<', 0);

                if ($expiredRequests->get()->count()) {

                    $expiredRequestsID = $expiredRequests->pluck('id');

                    $expiredRequests->update(['cab_requests.status' => 'CANCELLED']);
    
                    CabRequestFilter::whereIn('request_id', $expiredRequestsID)->delete();
    
                    (new SendPushController)->ProviderNotAvailable($latestRequest[0]->user_id);
                } 

            }

            if(!empty($request->latitude) && !empty($request->longitude)) {
                
                $driver->update([
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ]);

            }

            $response = [
                'account_status' => $driver->status,
                'service_status' => $driver->vehicle ? $driver->vehicle->status : 'OFFLINE',
                'requests' => $latestRequest,
            ];

            return $response;

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function assign_destroy($userRequest)
    {
        try {
            CabRequest::where('id', $userRequest->id)->update(['status' => 'CANCELLED']);
            CabRequestFilter::where('request_id', $userRequest->id)->delete();
            (new SendPushController)->ProviderNotAvailable($userRequest->user_id);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Unable to reject, Please try again later']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Connection Error']);
        }
    }

    public function assign_next_provider($userRequest) 
    {
        $driverID = auth('driver')->user()->id;
        CabRequestFilter::where('driver_id', $driverID)
            ->where('request_id', $userRequest->id)
            ->delete();

        try {
            $next_provider = CabRequestFilter::where('request_id', $userRequest->id)
                ->orderBy('id')
                ->firstOrFail();

            $userRequest->current_driver_id = $next_provider->driver_id;
            $userRequest->assigned_at = Carbon::now();
            $userRequest->save();

            (new SendPushController)->newRequest($next_provider->driver_id);
            
        } catch (ModelNotFoundException $e) {
            CabRequest::where('id', $userRequest->id)->update(['status' => 'CANCELLED']);
            (new SendPushController)->ProviderNotAvailable($userRequest->user_id); 
        }
    }

    public function calculate_distance(Request $request, $id)
    {
        $this->validate($request, [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        try {
            $driver = auth('driver')->user();

            $userRequest = CabRequest::where('status', 'PICKEDUP')
                ->where('driver_id', $driver->id)
                ->find($id);

            
            if ($userRequest->track_latitude && $userRequest->track_longitude) {

                $coordinate1 = new Coordinate($userRequest->track_latitude, $userRequest->track_longitude); 
                $coordinate2 = new Coordinate($request->latitude, $request->longitude); 

                $calculator = new Vincenty();

                $mydistance = $calculator->getDistance($coordinate1, $coordinate2); 

                $meters = round($mydistance);

                if ($meters >= 100) {
                    $traveldistance = round(($meters/1000),8);

                    $calulatedistance = $userRequest->track_distance + $traveldistance;

                    $userRequest->track_distance  = $calulatedistance;
                    $userRequest->distance        = $calulatedistance;
                    $userRequest->track_latitude  = $request->latitude;
                    $userRequest->track_longitude = $request->longitude;
                    $userRequest->save();
                }
            } else if (!$userRequest->track_latitude && !$userRequest->track_longitude) {
                $userRequest->distance             = 0;
                $userRequest->track_latitude      = $request->latitude;
                $userRequest->track_longitude     = $request->longitude;
                $userRequest->save();
            }

            return $userRequest;

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Something went wrong']);
        }
    }

    public function cancel(Request $request)
    {
        $this->validate($request, [
            'cancel_reason'=> 'max:255',
        ]);
        
        try {

            $userRequest = CabRequest::findOrFail($request->id);
            $Cancellable = ['SEARCHING', 'ACCEPTED', 'ARRIVED', 'STARTED', 'CREATED','SCHEDULED'];

            if(!in_array($userRequest->status, $Cancellable)) {
                return response()->json(['error' => 'Cannot cancel request at this stage!']);
            }

            $userRequest->status = "CANCELLED";
            $userRequest->cancel_reason = $request->cancel_reason;
            $userRequest->cancelled_by = "DRIVER";
            $userRequest->save();

            CabRequestFilter::where('request_id', $userRequest->id)->delete();

            DriverVehicle::where('driver_id',$userRequest->driver_id)
                ->update(['status' =>'ACTIVE', 'trip_type' => null, 'trip_id' => null]);

            (new SendPushController)->ProviderCancellRide($userRequest);

            return $userRequest;

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Something went wrong']);
        }
    }

    public function rate(Request $request, $id)
    {
        $this->validate($request, [
            'rating' => 'required|integer|in:1,2,3,4,5',
            'comment' => 'max:255',
        ]);
    
        try {
            $userRequest = CabRequest::where('id', $id)
                ->where('status', 'COMPLETED')
                ->firstOrFail();

            if($userRequest->rating) {
                $userRequest->rating->update([
                    'driver_rating' => $request->rating,
                    'driver_comment' => $request->comment,
                ]);
            } else {
                Rating::create([
                    'ratingable_id' => $userRequest->id,
                    'ratingable_type' => 'App\CabRequest',
                    'driver_id' => $userRequest->driver_id,
                    'user_id' => $userRequest->user_id,
                    'driver_rating' => $request->rating,
                    'driver_comment' => $request->comment
                ]);
            }

            $userRequest->update(['driver_rated' => 1]);

            CabRequestFilter::where('request_id', $id)->delete();

            DriverVehicle::where('driver_id', $userRequest->driver_id)
                ->update(['status' =>'ACTIVE', 'trip_type' => null, 'trip_id' => null]);

            $average = Rating::where('driver_id', $userRequest->driver_id)
                ->avg('driver_rating');

            $userRequest->user->update(['rating' => $average]);

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
        try {
            $Jobs = CabRequest::where('driver_id', Auth::guard('driver')->user()->id)
                ->where('status', 'SCHEDULED')
                ->with('car_type')
                ->get();

            if(!empty($Jobs)) {
                foreach ($Jobs as $key => $value) {
                    $Jobs[$key]->static_map = StaticMapUrl::generate($value);
                }
            }

            return $Jobs;
            
        } catch(\Exception $e) {
            return response()->json(['error' => "Something Went Wrong"]);
        }
    }

    public function history(Request $request)
    {
        $Jobs = CabRequest::where('driver_id', Auth::guard('driver')->user()->id)
                ->where('status', 'COMPLETED')
                ->orderBy('created_at','desc')
                ->with('payment')
                ->get();

        if(!empty($Jobs)){
            foreach ($Jobs as $key => $value) {
                $Jobs[$key]->static_map = StaticMapUrl::generate($value);
            }
        }
        return $Jobs;
    }

    public function accept(Request $request, $id)
    {
        try {
            $userRequest = CabRequest::findOrFail($id);
            if ($userRequest->status != "SEARCHING") {
                return response()->json(['error' => 'Request already under progress!']);
            }

            $driver_id = Auth::guard('driver')->user()->id;
            
            $userRequest->driver_id = $driver_id;
            $userRequest->current_driver_id = $driver_id;

            if ($userRequest->schedule_at) {
                $beforeschedule_time = strtotime($userRequest->schedule_at."- 1 hour");
                $afterschedule_time = strtotime($userRequest->schedule_at."+ 1 hour");

                $CheckScheduling = CabRequest::where('status', 'SCHEDULED')
                    ->where('driver_id', $driver_id)
                    ->whereBetween('schedule_at', [$beforeschedule_time, $afterschedule_time])
                    ->count();

                if ($CheckScheduling) {
                    return response()->json(['error' => trans('cabResponses.ride.request_already_scheduled')]);
                }

                CabRequestFilter::where('request_id', $userRequest->id)
                    ->where('driver_id', $driver_id)
                    ->update(['status' => 2]);

                $userRequest->status = "SCHEDULED";
                $userRequest->save();

            } else {

                $userRequest->status = "STARTED";
                $userRequest->save();

                DriverVehicle::where('driver_id', $userRequest->driver_id)
                    ->update(['status' => 'RIDING', 'trip_type' => 'CAB', 'trip_id' => $userRequest->id]);

                CabRequestFilter::where('request_id', $userRequest->id)
                    ->where('driver_id', '!=', $driver_id)->delete();
            }

            CabRequestFilter::where('request_id', '!=', $userRequest->id)
                ->where('driver_id', $driver_id )
                ->whereHas('request', function($query) {
                    $query->where('status', '<>', 'SCHEDULED');
                })
                ->delete(); 

            (new SendPushController)->RideAccepted($userRequest);

            return $userRequest->with('user')->get();

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Request ID does not exist.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Connection Error']);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required|in:ACCEPTED,STARTED,ARRIVED,PICKEDUP,DROPPED,PAYMENT,COMPLETED',
        ]);
 
        try {
            $userRequest = CabRequest::with('user')->findOrFail($id);

            if ($request->status == 'DROPPED' && $userRequest->payment_mode != 'CASH') {
                $userRequest->status = 'COMPLETED';
            } else if ($request->status == 'COMPLETED' && $userRequest->payment_mode == 'CASH') {
                $userRequest->status = $request->status;
                $userRequest->paid = 1;
                // DriverVehicle::where('driver_id',$userRequest->driver_id)
                    // ->update(['status' =>'ACTIVE']);
            } else {
                $userRequest->status = $request->status;

                if ($request->status == 'ARRIVED') {
                    (new SendPushController)->Arrived($userRequest);
                } else if ($request->status == 'PICKEDUP') {
                    if ($userRequest->is_track) {
                       $userRequest->distance = 0; 
                    }
                    $userRequest->started_at = Carbon::now();
                } else if ($request->status == 'DROPPED') {
                    if ($userRequest->is_track) {
                        $userRequest->d_latitude = $request->latitude ?: $userRequest->d_latitude;
                        $userRequest->d_longitude = $request->longitude ?: $userRequest->d_longitude;
                        $userRequest->d_address =  $request->address ?: $userRequest->d_address;
                    }
                    $userRequest->finished_at = Carbon::now();
                    $StartedDate  = date_create($userRequest->started_at);
                    $FinisedDate  = Carbon::now();
                    $TimeInterval = date_diff($StartedDate,$FinisedDate);
                    $MintuesTime  = $TimeInterval->i;
                    $userRequest->travel_time = $MintuesTime;
                    $userRequest->save();
                    $userRequest->invoice = $this->invoice($userRequest);
                    (new SendPushController)->Dropped($userRequest);
                }
            }
            
            $userRequest->save();
       
            return $userRequest;

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Request ID does not exist.']);
        }
    }

    public function destroy($id)
    {
        try {
            $userRequest = CabRequest::findOrFail($id);
            $this->assign_next_provider($userRequest);
            return $userRequest->with('user')->get();
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Request ID does not exist.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong, please try again.']);
        }
    }

    public function invoice($userRequest)
    {
        try {
            $car_type = CarType::findOrFail($userRequest->car_type_id);
            $kilometer = round($userRequest->distance/1000);
            $Fixed = $car_type->fixed;
            $Distance = 0;
            $minutes = 0;
            $Discount = 0;
            $Wallet = 0;
            $Surge = 0;
            $ProviderCommission = 0;
            $ProviderPay = 0;

            $calculator = $car_type->calculator;

            switch($calculator) {
                case "MIN":
                    $Distance = $car_type->minute * $minutes;
                    break;
                case "HOUR":
                    $Distance = $car_type->minute * 60;
                    break;
                case "DISTANCEMIN":
                    $Distance = ($kilometer * $car_type->price) + ($car_type->minute * $minutes);
                    break;
                case "DISTANCEHOUR":
                    $Distance = ($kilometer * $car_type->price) + ($car_type->minute * $minutes * 60);
                    break;
                default:
                    $Distance = ($kilometer * $car_type->price);
            }

            $commission = ($Distance + $Fixed) * ( $this->commission_percentage/100 );
            $Tax = ($Distance + $Fixed) * ( $this->tax_percentage/100 );
            $ProviderCommission = ($Distance + $Fixed) * ( $this->driver_commission_percentage/100 );
            $ProviderPay = ($Distance + $Fixed) - $ProviderCommission;

            $PromoCodeUsage = PromoCodeUsage::where('user_id', $userRequest->user_id)
                ->where('status', 'ADDED')
                ->first();

            if ($PromoCodeUsage) {
                $PromoCode = PromoCode::find($PromoCodeUsage->promo_code_id);
                $Discount = $PromoCode->discount;
                $PromoCodeUsage->status ='USED';
                $PromoCodeUsage->save();

                if ($PromoCode->discount_type == 'AMOUNT') {
                    $Total = $Fixed + $Distance + $Tax - $Discount;
                } else {
                    $Total = ($Fixed + $Distance + $Tax) - (($Fixed + $Distance + $Tax) * ($Discount/100));
                    $Discount = (($Fixed + $Distance + $Tax) * ($Discount/100));
                }
            } else {
                $Total = $Fixed + $Distance + $Tax - $Discount;
            }

            if ($userRequest->surge) {
                $Surge = ($this->surge_percentage/100) * $Total;
                $Total += $Surge;
            }

            if ($Total < 0) $Total = 0.00;

            $Payment = new CabRequestPayment;
            $Payment->request_id = $userRequest->id;

            $Payment->fixed = $Fixed + $Surge;
            $Payment->distance = $Distance;
            $Payment->commission = $commission;
            $Payment->surge = $Surge;
            $Payment->total = $Total;
            $Payment->driver_commission = $ProviderCommission;
            $Payment->driver_pay = $ProviderPay;
            if ($Discount != 0 && $PromoCodeUsage){
                $Payment->promo_code_id = $PromoCodeUsage->promo_code_id;
            }
            $Payment->discount = $Discount;

            if ($Discount  == ($Fixed + $Distance + $Tax)) {
                $userRequest->paid = 1;
            }

            if ($userRequest->use_wallet == 1 && $Total > 0) {
                $User = User::find($userRequest->user_id);
                $Wallet = $User->wallet_balance;
                if ($Wallet != 0) {

                    if ($Total > $Wallet) {

                        $Payment->wallet = $Wallet;
                        $Payable = $Total - $Wallet;
                        $User->update(['wallet_balance' => 0 ]);
                        $Payment->payable = abs($Payable);

                        WalletPassbook::create([
                          'user_id' => $userRequest->user_id,
                          'amount' => $Wallet,
                          'status' => 'DEBITED',
                          'via' => 'TRIP',
                        ]);

                        (new SendPushController)->ChargedWalletMoney($userRequest->user_id, currency($Wallet));

                    } else {
                        $Payment->payable = 0;
                        $WalletBalance = $Wallet - $Total;
                        $User->update(['wallet_balance' => $WalletBalance]);
                        $Payment->wallet = $Total;
                        
                        $Payment->payment_id = 'WALLET';
                        $Payment->payment_mode = $userRequest->payment_mode;

                        $userRequest->paid = 1;
                        $userRequest->status = 'COMPLETED';
                        $userRequest->save();

                        WalletPassbook::create([
                          'user_id' => $userRequest->user_id,
                          'amount' => $Total,
                          'status' => 'DEBITED',
                          'via' => 'TRIP',
                        ]);

                        (new SendPushController)->ChargedWalletMoney($userRequest->user_id, currency($Total));
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
            return response()->json(['error' => 'Car type does not exist.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong, please try again.']);
        }
    }

    public function history_details(Request $request)
    {
        $this->validate($request, [
            'request_id' => 'required|integer',
        ]); 
            
        $Jobs = CabRequest::where('id',$request->request_id)
            ->where('driver_id', Auth::guard('driver')->user()->id)
            ->with('payment','car_type','user','rating')
            ->get();
        if(!empty($Jobs)) {
            foreach ($Jobs as $key => $value) {
                $Jobs[$key]->static_map = StaticMapUrl::generate($value);
            }
        }
        return $Jobs;
    }

    public function upcoming_trips() 
    {
        try {
            $userRequest = CabRequest::ProviderUpcomingRequest(Auth::guard('driver')->user()->id)->get();
            if(!empty($userRequest)) {
                foreach ($userRequest as $key => $value) {
                    $userRequest[$key]->static_map = StaticMapUrl::generate($value);
                }
            }
            return $userRequest;
        }

        catch (\Exception $e) {
            return response()->json(['error' => trans('cabResponses.something_went_wrong')]);
        }
    }

    public function upcoming_details(Request $request)
    {
        $this->validate($request, [
            'request_id' => 'required|integer',
        ]);

        $Jobs = CabRequest::where('id',$request->request_id)
            ->where('driver_id', Auth::guard('driver')->user()->id)
            ->with('car_type','user')
            ->get();
        if(!empty($Jobs)) {
            foreach ($Jobs as $key => $value) {
                $Jobs[$key]->static_map = StaticMapUrl::generate($value);
            }
        }
        return $Jobs;
    }

    public function summary(Request $request)
    {
        try {
            $rides = CabRequest::where('driver_id', Auth::guard('driver')->user()->id)->count();
            $revenue = CabRequestPayment::whereHas('request', function($query) use ($request) {
                $query->where('driver_id', Auth::guard('driver')->user()->id);
            })
            ->sum('total');
            $cancel_rides = CabRequest::where('status','CANCELLED')->where('driver_id', Auth::guard('driver')->user()->id)->count();
            $scheduled_rides = CabRequest::where('status', 'SCHEDULED')->where('driver_id', Auth::guard('driver')->user()->id)->count();

            return response()->json([
                'rides' => $rides, 
                'revenue' => $revenue,
                'cancel_rides' => $cancel_rides,
                'scheduled_rides' => $scheduled_rides,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => trans('cabResponses.something_went_wrong')]);
        }

    }

}
