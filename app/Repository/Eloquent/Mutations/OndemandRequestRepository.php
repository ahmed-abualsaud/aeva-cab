<?php

namespace App\Repository\Eloquent\Mutations;

use App\Jobs\SendOtp;
use App\OndemandRequest;
use App\Mail\DefaultMail;
use App\OndemandRequestLine;
use App\OndemandRequestVehicle;
use App\Events\request_submitted;
use App\Jobs\SendPushNotification;
use App\Traits\HandleDeviceTokens;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repository\Eloquent\BaseRepository;

class OndemandRequestRepository extends BaseRepository
{
    use HandleDeviceTokens;

    public function __construct(OndemandRequest $model)
    {
        parent::__construct($model);
    }

    public function create(array $args)
    {
        DB::beginTransaction();
        try {
            $input = collect($args)->except(['directive', 'vehicles', 'lines'])->toArray();
            $request = $this->model->create($input);
            $this->createLines($args['lines'], $request->id);
            if (array_key_exists('vehicles', $args) && $args['vehicles'])
                $this->createVehicles($args['vehicles'], $request->id);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new CustomException(__('lang.create_request_failed'));
        }

        $this->smsRequest($request->id);
        
        // $this->broadcastRequest($request);

        // $this->mailRequest($request->id);

        return $request;
    }

    public function update(array $args)
    {
        try {
            $input = collect($args)->except(['id', 'directive', 'notify'])->toArray();
            $request = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new CustomException(__('lang.request_not_found'));
        }

        if (array_key_exists('status', $args) && $args['status']) 
            $this->updateStatus($request, $args);
        
        $request->update($input);

        return $request;
    }

    public function destroy(array $args)
    {
        return $this->model->whereIn('id', $args['id'])->delete();
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

    protected function updateStatus($request, $args)
    {
        if ($request->status === 'CANCELLED' || $request->status === 'REJECTED')
            throw new CustomException(__('lang.change_request_failed'));
        
        if ($args['status'] === 'CANCELLED' && $request->status !== 'PENDING') 
            throw new CustomException(__('lang.cancel_request_failed'));

        if (array_key_exists('notify', $args) && $args['notify']) {
            $responseMsg = 'Your ondemand request # ' 
                . $request->id . ' has been ' 
                . strtolower($args['status']);
    
            if (array_key_exists('response', $args) && $args['response']) 
                $responseMsg .= '. '. $args['response'];
            
            SendPushNotification::dispatch(
                $this->userToken($request->user_id), 
                $responseMsg,
                'Aeva On Demand',
                ['view' => 'OnDemandRequest', 'id' => $request->id]
            ); 
        }

        
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
        
        broadcast(new request_submitted('App.Admin', 'ondemand.request', $req));
    }

    protected function mailRequest($request_id)
    {
        $title = 'Aeva On Demand';
        $msg = __('lang.request_submitted');
        $view = 'emails.requests.default';
        $url = config('custom.app_url')."/ondemand/".$request_id;
        
        Mail::to(config('custom.mail_to_address'))
            ->send(new DefaultMail($msg, $title, $url, $view));
    }

    protected function smsRequest($request_id)
    {
        $phones = config('custom.otp_to_number');
        $msg = __('lang.request_ID_submitted', ['request_id' => $request_id]);

        SendOtp::dispatch($phones, $msg);
    }

}
