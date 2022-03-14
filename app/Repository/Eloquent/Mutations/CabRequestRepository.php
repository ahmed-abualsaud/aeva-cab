<?php

namespace App\Repository\Eloquent\Mutations;   

use App\Driver;
use App\Vehicle;
use App\CabRating;
use App\CabRequest;

use App\Helpers\StaticMapUrl;

use App\Events\AcceptCabRequest;
use App\Events\CabRequestStatusChanged;
use App\Events\CabRequestCancelled;

use App\Jobs\SendPushNotification;
use App\Exceptions\CustomException;

use App\Traits\HandleDeviceTokens;
use App\Traits\HandleDriverAttributes;

use App\Repository\Eloquent\BaseRepository;
use App\Repository\Mutations\CabRequestRepositoryInterface;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class CabRequestRepository extends BaseRepository implements CabRequestRepositoryInterface
{
    use HandleDeviceTokens, HandleDriverAttributes;

    /**
    * CabRequest constructor.
    *
    * @param CabRequest
    */
    public function __construct(CabRequest $model)
    {
        parent::__construct($model);
    }

    public function schedule(array $args)
    {
        $input = Arr::except($args, ['directive', 'user_name', 'distance', 'total_eta']);
        $args['next_free_time'] = date('Y-m-d H:i:s', 
            strtotime('+'.($args['total_eta'] + 300).' seconds', strtotime($args['schedule_time'])));

        if (!$this->isTimeValidated($args)) {
            throw new CustomException(__('lang.schedule_request_failed'));
        }

        $payload = [
            'summary' => [
                'distance' => $args['distance'],
                'total_eta' => $args['total_eta']
            ],
            'scheduled' => [
                'at' => date("Y-m-d H:i:s"),
                'user_name' => $args['user_name']
            ]
        ];

        $input['history'] = $payload;
        $input['status'] = 'SCHEDULED';
        $input['next_free_time'] = $args['next_free_time'];

        return $this->model->create($input); 
    }

    public function search(array $args)
    {
        if (array_key_exists('id', $args) && $args['id']) {
            return $this->searchScheduledRequest($args);
        } else {
            return $this->searchNewRequest($args);
        }
    }

    public function send(array $args) 
    {
        $request = $this->findRequest($args['id']);

        if ( $request->status != 'SEARCHING' ) {
            throw new CustomException(__('lang.request_drivers_failed'));
        }

        $vehicles = $request->history['searching']['result']['vehicles'];

        $filtered = Arr::where($vehicles, function ($value, $key) use ($args){
            return $value['car_type'] == $args['car_type'];
        });

        if ( $filtered == null ) {
            throw new CustomException(__('lang.unavailable_car_type'));
        }

        $payload = [
            'sending' => [
                'at' => date("Y-m-d H:i:s"),
            ]
        ];

        $input['status'] = 'SENDING';
        $input['costs'] = $filtered[0]['price'];
        $input['history'] = array_merge($request->history, $payload);
        
        $request = $this->updateRequest($request, $input);

        $driversIds = Arr::pluck($filtered, 'driver_id');

        SendPushNotification::dispatch(
            $this->driversToken($driversIds),
            __('lang.accept_request_body'),
            __('lang.accept_request'),
            ['view' => 'AcceptRequest', 'id' => $args['id']]
        );

        broadcast(new AcceptCabRequest($driversIds, $request));

        return $request;
    }

    protected function searchScheduledRequest(array $args) 
    {
        $request = $this->findRequest($args['id']);

        if ( $request->status == 'COMPLETED' || $request->status == 'CANCELLED' ) {
            throw new CustomException(__('lang.search_request_failed'));
        }

        $result = $this->checkPendingAndGetDrivers($request->toArray());

        $payload = [
            'searching' => [
                'at' => date("Y-m-d H:i:s"),
                'result' => $result
            ]
        ];

        $input['status'] = 'SEARCHING';
        $input['history'] = array_merge($request->history, $payload);

        $request = $this->updateRequest($request, $input);
        $request['result'] = $result;

        return $request;
    }

    protected function searchNewRequest(array $args) 
    {
        $input = Arr::except($args, ['directive', 'user_name', 'distance']);
        $result = $this->checkPendingAndGetDrivers($args);

        $payload = [
            'summary' => [
                'distance' => $args['distance']
            ],
            'searching' => [
                'at' => date("Y-m-d H:i:s"),
                'user_name' => $args['user_name'],
                'result' => $result
            ]
        ];

        $input['status'] = 'SEARCHING';
        $input['history'] = $payload;

        $request = $this->model->create($input);
        $request['result'] = $result;

        return $request;
    }

    protected function checkPendingAndGetDrivers(array $args)
    {
        $activeRequests = $this->model->wherePending($args['user_id'])->first();

        if($activeRequests) {
            throw new CustomException(__('lang.request_inprogress'));
        }

        $drivers = $this->getNearestDrivers($args['s_lat'], $args['s_lng']);

        if (!count($drivers) ) {
            throw new CustomException(__('lang.no_available_drivers'));
        }

        $vehicles = Vehicle::selectRaw('
            driver_vehicles.driver_id,
            car_models.name car_model,
            car_types.name as car_type,
            (car_types.fixed  + (car_types.price * ?) / 1000) as price,
            vehicles.license_plate as license,
            vehicles.photo'
            , [$args['distance']])
            ->join('car_types', 'car_types.id', '=', 'vehicles.car_type_id')
            ->join('car_models', 'car_models.id', '=', 'vehicles.car_model_id')
            ->join('driver_vehicles', 'driver_vehicles.vehicle_id', '=', 'vehicles.id')
            ->whereIn('driver_vehicles.driver_id', Arr::pluck($drivers, 'driver_id'))
            ->get();

        return ['drivers' => $drivers, 'vehicles' => $vehicles];
    }

    public function accept(array $args)
    {
        $request = $this->findRequest($args['id']);
        
        if ( $request->status != 'SENDING' ) {
            throw new CustomException(__('lang.accept_request_failed'));
        }

        $payload = [
            'accepted' => [
                'at' => date("Y-m-d H:i:s"),
            ]
        ];

        $args['status'] = 'ACCEPTED';
        $args['history'] = array_merge($request->history, $payload);

        $request = $this->updateRequest($request, $args);

        $this->updateDriverStatus($args['driver_id'], 'RIDING');

        SendPushNotification::dispatch(
            $this->userToken($request->user_id),
            __('lang.request_accepted_body'),
            __('lang.request_accepted'),
            ['view' => 'RequestAccepted', 'id' => $args['id']]
        );

        broadcast(new CabRequestStatusChanged($request));

        return $request;
    }

    public function arrived(array $args)
    {
        $request = $this->findRequest($args['id']);
        
        if ( $request->status != 'ACCEPTED' ) {
            throw new CustomException(__('lang.update_request_status_failed'));
        }

        $payload = [
            'arrived' => [
                'at' => date("Y-m-d H:i:s"),
            ]
        ];

        $args['status'] = 'ARRIVED';
        $args['history'] = array_merge($request->history, $payload);

        $request = $this->updateRequest($request, $args);

        SendPushNotification::dispatch(
            $this->userToken($request->user_id),
            __('lang.driver_arrived_body'),
            __('lang.driver_arrived'),
            ['view' => 'StartRide', 'id' => $args['id']]
        );

        broadcast(new CabRequestStatusChanged($request));

        return $request;
    }

    public function start(array $args)
    {
        $request = $this->findRequest($args['id']);
        
        if ( $request->status != 'ARRIVED' ) {
            throw new CustomException(__('lang.start_ride_failed'));
        }

        $payload = [
            'started' => [
                'at' => date("Y-m-d H:i:s"),
            ]
        ];

        $args['status'] = 'STARTED';
        $args['history'] = array_merge($request->history, $payload);

        $request = $this->updateRequest($request, $args);
        $this->createCabRating($request);
        SendPushNotification::dispatch(
            $this->userToken($request->user_id),
            __('lang.ride_started_body'),
            __('lang.ride_started'),
            ['view' => 'RideStarted', 'id' => $args['id']]
        );

        broadcast(new CabRequestStatusChanged($request));

        return $request;
    }

    public function end(array $args)
    {
        $request = $this->findRequest($args['id']);
        
        if ( $request->status != 'STARTED' || $request->paid == false) {
            throw new CustomException(__('lang.end_ride_failed'));
        }

        $payload = [
            'completed' => [
                'at' => date("Y-m-d H:i:s"),
            ]
        ];

        $args['status'] = 'COMPLETED';
        $args['history'] = array_merge($request->history, $payload);
        $args['map_url'] = StaticMapUrl::generatePolylines($request);

        $request = $this->updateRequest($request, $args);

        $this->updateDriverStatus($request->driver_id, 'ONLINE');

        SendPushNotification::dispatch(
            $this->userToken($request->user_id),
            __('lang.ride_ended_body'),
            __('lang.ride_ended'),
            ['view' => 'RideEnded', 'id' => $args['id']]
        );

        broadcast(new CabRequestStatusChanged($request));

        return $request;
    }

    public function cancel(array $args)
    {
        $request = $this->findRequest($args['id']);
        
        if ( in_array($request->status, ['STARTED', 'COMPLETED']) ) {
            throw new CustomException(__('lang.cancel_cab_request_failed'));
        }

        $payload = [
            'cancelled' => [
                'at' => date("Y-m-d H:i:s"),
                'by' => $args['cancelled_by'],
                'reason' => array_key_exists('cancel_reason', $args) ? $args['cancel_reason'] : "Unknown",
            ]
        ];
        $args['status'] = 'CANCELLED';
        $args['history'] = array_merge($request->history, $payload);

        $request = $this->updateRequest($request, $args);

        $this->updateDriverStatus($request->driver_id, 'ONLINE');

        if (strtolower($args['cancelled_by']) == 'user' && $request->driver_id) {
            $token = $this->driverToken($request->driver_id);
            broadcast(new CabRequestCancelled('user', $request));
        }

        if (strtolower($args['cancelled_by']) == 'driver') {
            $token = $this->userToken($request->user_id);
            broadcast(new CabRequestCancelled('driver', $request));        
        }

        SendPushNotification::dispatch(
            $token,
            __('lang.request_cancelled_body'),
            __('lang.request_cancelled'),
            ['view' => 'CancelRequest', 'id' => $args['id']]
        );

        return $request;
    }

    public function reset(array $args)
    {
        $requests = $this->model->where($args['issuer_type'].'_id', $args['issuer_id'])
                    ->where(function ($query) {
                        $query->where('status', 'SEARCHING')
                              ->orWhere('status', 'SENDING');
                    });

        $ret = $requests->get();
        
        $payload = [
            'cancelled' => [
                'at' => date("Y-m-d H:i:s"),
                'by' => $args['issuer_type'],
                'reason' => "Reset Request",
            ]
        ];

        foreach( $requests->get()->toArray() as $request)
        {   $model = with(new CabRequest)->newInstance($request, true);
            $model->update([
                'status'  => 'CANCELLED',
                'history' => array_merge($request['history'], $payload)
            ]);
        }
        
        return $ret;
    }

    public function updateDriverCabStatus(array $args)
    {
        return $this->updateDriverStatus($args['driver_id'], $args['cab_status']);
    }

    protected function updateRequest($request, $args) 
    {
        $input = Arr::except($args, ['id', 'directive', 'cancelled_by', 'cancel_reason']);

        $request->update($input);

        return $request;
    }

    protected function findRequest($id) 
    {
        try {
            return $this->model->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.request_not_found'));
        }
    }

    protected function getNearestDrivers($lat, $lng) 
    {
        $radius = config('custom.seats_search_radius');

        $drivers = Driver::selectRaw('id AS driver_id, name, phone, avatar,
            ST_Distance_Sphere(point(longitude, latitude), point(?, ?))
            as distance
            ', [$lng, $lat]
            )
            ->having('distance', '<=', $radius)
            ->where('cab_status', 'ONLINE')
            ->orderBy('distance','asc')
            ->take(5)
            ->get();
        
        return $drivers;
    }

    protected function createCabRating($args) {
        $input = [
            'request_id' => $args['id'],
            'user_id' => $args['user_id'],
            'driver_id' => $args['driver_id'],
            'trip_time' => date('Y-m-d H:i:s')
        ];
        CabRating::create($input);
    }

    protected function isTimeValidated($args)
    {
        $occupiedPeriods = $this->model
            ->select('schedule_time','next_free_time')
            ->whereScheduled($args['user_id'])
            ->whereRaw('
                (? >= schedule_time AND ? < next_free_time) OR 
                (? >= schedule_time AND ? < next_free_time)
            ', [
                $args['schedule_time'], $args['schedule_time'], 
                $args['next_free_time'], $args['next_free_time']
            ])
            ->first();
        
        if($occupiedPeriods || time() > strtotime($args['schedule_time'])) {
            return false;
        }
        return true;
    }
}