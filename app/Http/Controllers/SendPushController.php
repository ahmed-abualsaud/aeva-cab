<?php

namespace App\Http\Controllers;

use App\User;
use App\DeviceToken;
use App\Jobs\PushNotification;

class SendPushController extends Controller
{
	/**
     * New Ride Accepted by a Driver.
     *
     * @return void
     */
    public function RideAccepted($request){
    	return $this->sendPushToUser($request->user_id, trans('cabResponses.push.request_accepted'));
    }

    /**
     * Driver Arrived at your location.
     *
     * @return void
     */
    public function user_schedule($user){

        return $this->sendPushToUser($user, trans('cabResponses.push.schedule_start'));
    }

    /**
     * New Incoming request
     *
     * @return void
     */
    public function provider_schedule($driver){

        return $this->sendPushToProvider($driver, trans('cabResponses.push.schedule_start'));

    }

    /**
     * New Ride Accepted by a Driver.
     *
     * @return void
     */
    public function UserCancellRide($request){

        return $this->sendPushToProvider($request->driver_id, trans('cabResponses.push.user_cancelled'));
    }


    /**
     * New Ride Accepted by a Driver.
     *
     * @return void
     */
    public function ProviderCancellRide($request){

        return $this->sendPushToUser($request->user_id, trans('cabResponses.push.provider_cancelled'));
    }

    /**
     * Driver Arrived at your location.
     *
     * @return void
     */
    public function Arrived($request){

        return $this->sendPushToUser($request->user_id, trans('cabResponses.push.arrived'));
    }

     /**
     * Driver Arrived at your location.
     *
     * @return void
     */
    public function Dropped($request){
        return $this->sendPushToUser($request->user_id, trans('cabResponses.push.dropped').env('CURRENCY', 'EGP').$request->payment->total.' by '.$request->payment_mode);
    }

    /**
     * Money added to user wallet.
     *
     * @return void
     */
    public function ProviderNotAvailable($user_id){

        return $this->sendPushToUser($user_id,trans('cabResponses.push.provider_not_available'));
    }

    /**
     * New Incoming request
     *
     * @return void
     */
    public function IncomingRequest($driver)
    {
        return $this->sendPushToProvider($driver, trans('cabResponses.push.incoming_request'));
    }
    

    /**
     * Driver Documents verfied.
     *
     * @return void
     */
    public function DocumentsVerfied($driver_id){

        return $this->sendPushToProvider($driver_id, trans('cabResponses.push.document_verfied'));
    }


    /**
     * Money added to user wallet.
     *
     * @return void
     */
    public function WalletMoney($user_id, $money){
        return $this->sendPushToUser($user_id, $money.' '.trans('cabResponses.push.added_money_to_wallet'));
    }

    /**
     * Money charged from user wallet.
     *
     * @return void
     */
    public function ChargedWalletMoney($user_id, $money){
        return $this->sendPushToUser($user_id, $money.' '.trans('cabResponses.push.charged_from_wallet'));
    }

    /**
     * Sending Push to a user Device.
     *
     * @return void
     */
    public function sendPushToUser($user_id, $push_message){
    	$devices = DeviceToken::where('tokenable_id', $user_id)
            ->where('tokenable_type', 'App\User')
            ->select('device_id')
            ->pluck('device_id');

        if ($devices) {
            PushNotification::dispatch($devices, $push_message);
        }
    }

    /**
     * Sending Push to a driver Device.
     *
     * @return void
     */
    public function sendPushToProvider($driver_id, $push_message)
    {
        if (is_array($driver_id)) {
            $devices = DeviceToken::whereIn('tokenable_id', $driver_id)
            ->where('tokenable_type', 'App\Driver')
            ->select('device_id')
            ->pluck('device_id');
        } else {
            $devices = DeviceToken::where('tokenable_id', $driver_id)
            ->where('tokenable_type', 'App\Driver')
            ->select('device_id')
            ->pluck('device_id');
        }
    	
        if ($devices) {
            PushNotification::dispatch($devices, $push_message);
        }

    }

}
