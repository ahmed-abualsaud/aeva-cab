<?php

namespace App\GraphQL\Mutations;

use App\Jobs\SendOtp;
use App\OndemandRequest;
use App\Mail\DefaultMail;
use App\OndemandRequestLine;
use App\OndemandRequestVehicle;
use App\Events\RequestSubmitted;
use App\Jobs\SendPushNotification;
use App\Traits\HandleDeviceTokens;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OndemandRequestResolver
{
    use HandleDeviceTokens;
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        DB::beginTransaction();
        try {
            $input = collect($args)->except(['directive', 'vehicles', 'lines'])->toArray();
            $request = OndemandRequest::create($input);
            $this->createLines($args['lines'], $request->id);
            if (array_key_exists('vehicles', $args) && $args['vehicles'])
                $this->createVehicles($args['vehicles'], $request->id);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new CustomException('We could not able to create this request!');
        }

        $this->broadcastRequest($request);

        $this->smsRequest($request->id);

        // $this->mailRequest($request->id);

        return $request;
    }

    protected function createVehicles($vehicles, $requestId)
    {
        $vehicles_arr = ['request_id' => $requestId];
        foreach($vehicles as $vehicle) {
            $vehicles_arr['car_type_id'] = $vehicle['car_type_id'];
            $vehicles_arr['car_model_id'] = $vehicle['car_model_id'];
            $vehicles_arr['count'] = $vehicle['count'];
            $vehicles_data[] =  $vehicles_arr;
        } 
        OndemandRequestVehicle::insert($vehicles_data);
    }

    protected function createLines($lines, $requestId)
    {
        $lines_arr = ['request_id' => $requestId];
        foreach($lines as $line) {
            $lines_arr['from_lat'] = $line['from_lat'];
            $lines_arr['from_lng'] = $line['from_lng'];
            $lines_arr['to_lat'] = $line['to_lat'];
            $lines_arr['to_lng'] = $line['to_lng'];
            $lines_arr['from_address'] = $line['from_address'];
            $lines_arr['to_address'] = $line['to_address'];
            $lines_data[] = $lines_arr;
        } 
        OndemandRequestLine::insert($lines_data);
    }

    public function update($_, array $args)
    {
        try {
            $input = collect($args)->except(['id', 'directive', 'notify'])->toArray();
            $request = OndemandRequest::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new CustomException('The provided request ID is not found.');
        }

        if (array_key_exists('status', $args) && $args['status']) 
            $this->updateStatus($request, $args);
        
        $request->update($input);

        return $request;
    }

    protected function updateStatus($request, $args)
    {
        if ($request->status === 'CANCELLED' || $request->status === 'REJECTED')
            throw new CustomException('Request status can not be changed.');
        
        if ($args['status'] === 'CANCELLED' && $request->status !== 'PENDING') 
            throw new CustomException('This request can not be cancelled.');

        if (array_key_exists('notify', $args) && $args['notify']) {
            $responseMsg = 'Your ondemand request # ' 
                . $request->id . ' has been ' 
                . strtolower($args['status']);
    
            if (array_key_exists('response', $args) && $args['response']) 
                $responseMsg .= '. '. $args['response'];
            
            SendPushNotification::dispatch(
                $this->userToken($request->user_id), 
                $responseMsg,
                'Qruz On Demand',
                ['view' => 'OnDemandRequest', 'id' => $request->id]
            ); 
        }

        
    }

    public function destroy($_, array $args)
    {
        return OndemandRequest::whereIn('id', $args['id'])->delete();
    }

    protected function broadcastRequest($request)
    {
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

    protected function mailRequest($request_id)
    {
        $title = 'Qruz On Demand';
        $msg = 'New On-Demand request has been submitted';
        $view = 'emails.requests.default';
        $url = config('custom.app_url')."/ondemand/".$request_id;
        
        Mail::to(config('custom.mail_to_address'))
            ->send(new DefaultMail($msg, $title, $url, $view));
    }

    protected function smsRequest($request_id)
    {
        $phones = '01110782632,01099637684';
        $msg = 'New On-Demand request # '.$request_id.' has been submitted';

        SendOtp::dispatch($phones, $msg);

    }

}
