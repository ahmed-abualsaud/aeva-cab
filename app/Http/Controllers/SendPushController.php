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

        return $this->sendPushToDriver($driver, trans('cabResponses.push.schedule_start'));

    }

    /**
     * New Ride Accepted by a Driver.
     *
     * @return void
     */
    public function UserCancellRide($request)
    {
        return $this->sendPushToDriver($request->driver_id, trans('cabResponses.push.user_cancelled'));
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
    public function Dropped($request)
    {
        return $this->sendPushToUser($request->user_id, trans('cabResponses.push.dropped').' '.$request->payment->total.' EGP by '.$request->payment_mode);
    }

    /**
     * Money added to user wallet.
     *
     * @return void
     */
    public function ProviderNotAvailable($user_id)
    {
        return $this->sendPushToUser($user_id, trans('cabResponses.push.provider_not_available'));
    }
    
    public function DocumentsVerfied($driver_id)
    {
        return $this->sendPushToDriver($driver_id, trans('cabResponses.push.document_verfied'));
    }

    public function newRequest($driver_id)
    {
        return $this->sendPushToDriver($driver_id, trans('cabResponses.push.incoming_request'));
    }

    public function WalletMoney($user_id, $money)
    {
        return $this->sendPushToUser($user_id, $money.' '.trans('cabResponses.push.added_money_to_wallet'));
    }

    public function ChargedWalletMoney($user_id, $money){
        return $this->sendPushToUser($user_id, $money.' '.trans('cabResponses.push.charged_from_wallet'));
    }

    public function sendPushToUser($user_id, $push_message){

        if (is_array($user_id)) {
            $devices = DeviceToken::whereIn('tokenable_id', $user_id)
            ->where('tokenable_type', 'App\User')
            ->select('device_id')
            ->pluck('device_id')
            ->toArray();
        } else {
            $devices = DeviceToken::where('tokenable_id', $user_id)
                ->where('tokenable_type', 'App\User')
                ->select('device_id')
                ->pluck('device_id')
                ->toArray();
        }

        if (count($devices)) {
            PushNotification::dispatch($devices, $push_message);
        }
    }

    /**
     * Sending Push to a driver Device.
     *
     * @return void
     */
    public function sendPushToDriver($driver_id, $push_message)
    {
        if (is_array($driver_id)) {
            $devices = DeviceToken::whereIn('tokenable_id', $driver_id)
                ->where('tokenable_type', 'App\Driver')
                ->select('device_id')
                ->pluck('device_id')
                ->toArray();
        } else {
            $devices = DeviceToken::where('tokenable_id', $driver_id)
                ->where('tokenable_type', 'App\Driver')
                ->select('device_id')
                ->pluck('device_id')
                ->toArray();
        }
    	
        if (count($devices)) {
            PushNotification::dispatch($devices, $push_message);
        }

    }

}
