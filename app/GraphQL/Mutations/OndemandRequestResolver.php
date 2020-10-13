<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\OndemandRequest;
use App\Mail\DefaultMail;
use App\OndemandRequestLine;
use App\OndemandRequestVehicle;
use App\Events\RequestSubmitted;
use App\Jobs\SendPushNotification;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OndemandRequestResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        try {
            $input = collect($args)->except(['directive', 'vehicles', 'lines'])->toArray();
            $request = OndemandRequest::create($input);
 
            $vehicles_data = array(); 
            $vehicles_arr = array();
            foreach($args['vehicles'] as $vehicle) {
                $vehicles_arr['request_id'] = $request->id;
                $vehicles_arr['car_type_id'] = $vehicle['car_type_id'];
                $vehicles_arr['car_model_id'] = $vehicle['car_model_id'];
                $vehicles_arr['count'] = $vehicle['count'];
                array_push($vehicles_data, $vehicles_arr);
            } 
            $vehicles = OndemandRequestVehicle::insert($vehicles_data);

            $lines_data = array(); 
            $lines_arr = array();
            foreach($args['lines'] as $line) {
                $lines_arr['request_id'] = $request->id;
                $lines_arr['from_lat'] = $line['from_lat'];
                $lines_arr['from_lng'] = $line['from_lng'];
                $lines_arr['to_lat'] = $line['to_lat'];
                $lines_arr['to_lng'] = $line['to_lng'];
                $lines_arr['from_address'] = $line['from_address'];
                $lines_arr['to_address'] = $line['to_address'];
                array_push($lines_data, $lines_arr);
            } 
            $lines = OndemandRequestLine::insert($lines_data);
        } catch (\Exception $e) {
            throw new CustomException('We could not able to create this request.' . $e->getMessage());
        }

        $this->broadcastRequest($request);

        return $request;
    }

    public function update($_, array $args)
    {
        $input = collect($args)->except(['id', 'directive'])->toArray();

        try {
            $request = OndemandRequest::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new CustomException('The provided request ID is not found.');
        }

        if (array_key_exists('status', $args) && $args['status']) {
            
            if ($request->status === 'CANCELLED' || $request->status === 'REJECTED') {
                throw new CustomException('Request status can not be changed.');
            }
            
            if ($args['status'] === 'CANCELLED' && $request->status !== 'PENDING') {
                throw new CustomException('This request can not be cancelled.');
            }

            if ($args['status'] !== 'CANCELLED') {
                $token = User::where('id', $request->user_id)
                    ->select('device_id')
                    ->pluck('device_id')
                    ->toArray();
                
                // $token = DeviceToken::where('tokenable_id', $request->user_id)
                //     ->where('tokenable_type', 'App\User')
                //     ->select('device_id')
                //     ->pluck('device_id')
                //     ->toArray();
    
                $responseTitle = 'Your Ondemand request ID ' . $request->id . ' has been ' . strtolower($args['status']);
                $responseMsg = $responseTitle;
                if ($args['response']) $responseMsg .= '. '. $args['response'];
    
                SendPushNotification::dispatch($token, $responseMsg);
                Mail::to($request->user->email)
                    ->send(new DefaultMail($responseMsg, $responseTitle));
            } else {
                $title = "On-Demand Request Cancelled";
                $message = "On-Demand request ID ". $request->id ." has been cancelled by user";
                if (array_key_exists('comment', $args) && $args['comment']) { 
                    $message .= ". ".$args['comment'];
                }

                $this->mailRequest($message, $title, $request->id);
            }
        }

        $request->update($input);

        return $request;
    }

    public function destroy($_, array $args)
    {
        return OndemandRequest::whereIn('id', $args['id'])->forceDelete();
    }

    protected function broadcastRequest($request)
    {
        $title = "New On-Demand Request";
        $message = "New On-Demand request has been submitted!";

        $this->mailRequest($message, $title, $request->id);

        $req = [
            'id' => $request->id,
            'verb' => $request->verb,
            'status' => 'PENDING',
            'created_at' => date("Y-m-d H:i:s"),
            'read_at' => null,
            '__typename' => 'OndemandRequest'
        ];
        
        broadcast(new RequestSubmitted('App.Admin', 'ondemand.request', $req));
    }

    protected function mailRequest($message, $title, $request_id)
    {
        $view = 'emails.requests.default';
        $url = config('custom.app_url')."/ondemand/".$request_id;
        
        Mail::to(config('custom.mail_to_address'))
            ->send(new DefaultMail($message, $title, $url, $view));
    }
}
