<?php

namespace Aeva\Cab\Domain\Repository\Eloquent\Mutations;   

use App\Driver;
use App\Vehicle;
use App\CarType;

use App\Helpers\ResizableMapUrl;

use App\Jobs\SendPushNotification;
use App\Exceptions\CustomException;

use App\Traits\HandleDriverAttributes;

use Aeva\Cab\Domain\Models\CabRating;
use Aeva\Cab\Domain\Models\CabRequest;

use Aeva\Cab\Domain\Traits\HandleDeviceTokens;

use Aeva\Cab\Domain\Events\AcceptCabRequest;
use Aeva\Cab\Domain\Events\CabRequestCancelled;
use Aeva\Cab\Domain\Events\CabRequestStatusChanged;

use Aeva\Cab\Domain\Repository\Eloquent\BaseRepository;
use Aeva\Cab\Domain\Repository\Mutations\CabRequestRepositoryInterface;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class CabRequestRepository extends BaseRepository implements CabRequestRepositoryInterface
{
    use HandleDeviceTokens;
    use HandleDriverAttributes;

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
        $input['status'] = 'Scheduled';
        $input['next_free_time'] = $args['next_free_time'];

        return $this->model->create($input); 
    }

    public function search(array $args)
    {
        return $this->searchNewRequest($args);
    }

    public function send(array $args) 
    {
        $request = $this->findRequest($args['id']);

        if ( $request->status != 'Searching' ) {
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
                'chosen_car_type' => $args['car_type']
            ]
        ];

        $input['status'] = 'Sending';
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

    public function accept(array $args)
    {
        $request = $this->findRequest($args['id']);
        
        if ( $request->status != 'Sending' ) {
            throw new CustomException(__('lang.accept_request_failed'));
        }

        $payload = [
            'accepted' => [
                'at' => date("Y-m-d H:i:s"),
            ]
        ];

        $args['status'] = 'Accepted';
        $args['history'] = array_merge($request->history, $payload);

        if ( !array_key_exists('vehicle_id', $args) || $args['vehicle_id'] == null ) {
            $vehicles = $request->history['searching']['result']['vehicles'];
            $vehicle = Arr::where($vehicles, function ($value, $key) use ($args){
                return $value['driver_id'] == $args['driver_id'];
            });
            $args['vehicle_id'] = $vehicle[0]['vehicle_id'];
        }

        $request = $this->updateRequest($request, $args);

        $this->updateDriverStatus($args['driver_id'], 'Riding');

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
        
        if ( $request->status != 'Accepted' ) {
            throw new CustomException(__('lang.update_request_status_failed'));
        }

        $payload = [
            'arrived' => [
                'at' => date("Y-m-d H:i:s"),
            ]
        ];

        $args['status'] = 'Arrived';
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
        
        if ( $request->status != 'Arrived' ) {
            throw new CustomException(__('lang.start_ride_failed'));
        }

        $payload = [
            'started' => [
                'at' => date("Y-m-d H:i:s"),
            ]
        ];

        $args['status'] = 'Started';
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
        
        if ( $request->status != 'Started' || $request->paid == false) {
            throw new CustomException(__('lang.end_ride_failed'));
        }

        $payload = [
            'completed' => [
                'at' => date("Y-m-d H:i:s"),
            ]
        ];

        $vehicles = $request->history['searching']['result']['vehicles'];
        $vehicle = Arr::where($vehicles, function ($value, $key) use ($request){
            return $value['driver_id'] == $request->driver_id;
        });

        $args['costs'] = $this->calculateCosts($args['distance'], $args['duration'], $vehicle[0]['car_type_id']);

        $args['status'] = 'Completed';
        $args['history'] = array_merge($request->history, $payload);
        $args['map_url'] = ResizableMapUrl::generatePolylines($request);

        $request = $this->updateRequest($request, $args);

        $this->updateDriverStatus($request->driver_id, 'Online');

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
        
        if ( in_array($request->status, ['Started', 'Completed']) ) {
            throw new CustomException(__('lang.cancel_cab_request_failed'));
        }

        $payload = [
            'cancelled' => [
                'at' => date("Y-m-d H:i:s"),
                'by' => $args['cancelled_by'],
                'reason' => array_key_exists('cancel_reason', $args) ? $args['cancel_reason'] : "Unknown",
            ]
        ];
        
        $args['history'] = array_merge($request->history, $payload);
        $this->updateDriverStatus($request->driver_id, 'Online');

        $token = null;
        if (strtolower($args['cancelled_by']) == 'user') {
            $args['status'] = 'Cancelled';
            if ($request->driver_id) {
                $token = $this->driversToken($request->driver_id);
            }
        }

        if (strtolower($args['cancelled_by']) == 'driver') {
            $args['status'] = 'Searching';
            $token = $this->userToken($request->user_id);
        }

        $request = $this->updateRequest($request, $args);
        $socketRequest = clone $request;
        $socketRequest->status = 'Cancelled';

        if ($token) {
            SendPushNotification::dispatch(
                $token,
                __('lang.request_cancelled_body'),
                __('lang.request_cancelled'),
                ['view' => 'CancelRequest', 'id' => $args['id']]
            );
            broadcast(new CabRequestCancelled(strtolower($args['cancelled_by']), $socketRequest));
        }

        return $request;
    }

    public function reset(array $args)
    {
        return $this->model->where($args['issuer_type'].'_id', $args['issuer_id'])
            ->where(function ($query) {
                $query->where('status', 'Searching')
                        ->orWhere('status', 'Sending');
            })
            ->delete();        
    }

    public function updateDriverCabStatus(array $args)
    {
        return $this->updateDriverStatus($args['driver_id'], $args['cab_status']);
    }

    public function redirect(array $args)
    {
        $request = $this->findRequest($args['id']);
        
        if ( $request->status == 'Searching' ) {
            $request = $this->re_search($request, $args);
        }

        if ( $request->status == 'Sending' ) {
            $request = $this->re_send($request, $args);
        }

        if ( $request->status == 'Accepted' ) {
            $request = $this->re_accept($request, $args);
        }

        if ( $request->status == 'Arrived' ) {
            $request = $this->re_arrived($request, $args);
        }

        if ( $request->status == 'Started' ) {
            $request = $this->re_start($request, $args);
        }

        return $request;
    }

    protected function re_search($request, $args) 
    {
        $args['s_lat'] = $request->s_lat;
        $args['s_lng'] = $request->s_lng;
        $result = $this->getNearestDriversWithVehicles($args);

        $payload = [
            'summary' => [
                'distance' => $args['distance'],
                'duration' => $args['duration']
            ],
            'searching' => [
                'at' => $request->history['searching']['at'],
                'user_name' => $request->history['searching']['user_name'],
                'result' => $result
            ],
            're_search' => [
                'at' => date("Y-m-d H:i:s")
            ]
        ];

        $input['d_lat'] = $args['d_lat'];
        $input['d_lng'] = $args['d_lng'];
        $input['history'] = array_merge($request->history, $payload);

        $request = $this->updateRequest($request, $input);
        $request['result'] = $result;

        return $request;
    }

    protected function re_send($request, $args)
    {
        $args['s_lat'] = $request->s_lat;
        $args['s_lng'] = $request->s_lng;
        $result = $this->getNearestDriversWithVehicles($args);

        $filtered = Arr::where($result['vehicles']->toArray(), function ($value, $key) use ($request){
            return $value['car_type'] == $request->history['sending']['chosen_car_type'];
        });

        $payload = [
            'summary' => [
                'distance' => $args['distance'],
                'duration' => $args['duration']
            ],
            'searching' => [
                'at' => $request->history['searching']['at'],
                'user_name' => $request->history['searching']['user_name'],
                'result' => $result
            ],
            're_send' => [
                'at' => date("Y-m-d H:i:s")
            ]
        ];

        $input['d_lat'] = $args['d_lat'];
        $input['d_lng'] = $args['d_lng'];
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

    protected function re_accept($request, $args)
    {
        return $this->refresh($request, $args, 'accept');
    }

    protected function re_arrived($request, $args) 
    {
        return $this->refresh($request, $args, 'arrive');
    }

    protected function re_start($request, $args) 
    {
        return $this->refresh($request, $args, 'start');
    }

    protected function refresh($request, $args, $action)
    {
        $result = $request->history['searching']['result'];
        $prices = $this->calculateCosts($args['distance'], $args['duration'], Arr::pluck($result['vehicles'], 'car_type_id'));
        $vehicles = collect($result['vehicles'])->keyBy('car_type_id')->toArray();

        foreach ($vehicles as $carTypeId => $vehicle) {
            $vehicles[$carTypeId]['price'] = $prices[$carTypeId]['costs'];
        }

        [$carTypeId, $vehicles] = Arr::divide($vehicles);
        $result['vehicles'] = $vehicles;

        $filtered = Arr::where($result['vehicles'], function ($value, $key) use ($request){
            return $value['car_type'] == $request->history['sending']['chosen_car_type'];
        });

        $payload = [
            'summary' => [
                'distance' => $args['distance'],
                'duration' => $args['duration']
            ],
            'searching' => [
                'at' => $request->history['searching']['at'],
                'user_name' => $request->history['searching']['user_name'],
                'result' => $result
            ],
            're_'.$action => [
                'at' => date("Y-m-d H:i:s")
            ]
        ];

        $input['d_lat'] = $args['d_lat'];
        $input['d_lng'] = $args['d_lng'];
        $input['costs'] = ($action == 'start') ? $filtered[0]['price'] + $request->costs : $filtered[0]['price']; 
        $input['history'] = array_merge($request->history, $payload);

        $request = $this->updateRequest($request, $input);

        SendPushNotification::dispatch(
            $this->driversToken($request->driver_id),
            __('lang.ride_redirection_body'),
            __('lang.ride_redirection'),
            ['view' => 'RideRedirection', 'id' => $args['id']]
        );

        $socketRequest = clone $request;
        $socketRequest->status = 'Redirected';

        broadcast(new CabRequestCancelled('user', $socketRequest));

        return $request;
    }

    protected function searchNewRequest(array $args) 
    {
        $input = Arr::except($args, ['directive', 'user_name', 'distance', 'duration']);

        $activeRequests = $this->model->wherePending($args['user_id'])->first();

        if($activeRequests) {
            throw new CustomException(__('lang.request_inprogress'));
        }

        $result = $this->getNearestDriversWithVehicles($args);

        $payload = [
            'summary' => [
                'distance' => $args['distance'],
                'duration' => $args['duration']
            ],
            'searching' => [
                'at' => date("Y-m-d H:i:s"),
                'user_name' => $args['user_name'],
                'result' => $result
            ]
        ];

        $input['status'] = 'Searching';
        $input['history'] = $payload;

        $request = $this->model->create($input);
        $request['result'] = $result;

        return $request;
    }

    protected function getNearestDriversWithVehicles(array $args)
    {
        $drivers = $this->getNearestDrivers($args['s_lat'], $args['s_lng']);

        if (!count($drivers) ) {
            throw new CustomException(__('lang.no_available_drivers'));
        }

        $vehicles = Vehicle::selectRaw('
            driver_vehicles.vehicle_id,
            driver_vehicles.driver_id,
            car_models.name car_model,
            car_types.id as car_type_id,
            car_types.name as car_type,
            (car_types.base_fare  + ((car_types.distance_price * ?) / 1000) + ((car_types.duration_price * car_types.surge_factor * ?) / 60)) as price,
            vehicles.license_plate as license,
            vehicles.color,
            vehicles.photo'
            , [$args['distance'], $args['duration']])
            ->join('car_types', 'car_types.id', '=', 'vehicles.car_type_id')
            ->join('car_models', 'car_models.id', '=', 'vehicles.car_model_id')
            ->join('driver_vehicles', 'driver_vehicles.vehicle_id', '=', 'vehicles.id')
            ->whereIn('driver_vehicles.driver_id', Arr::pluck($drivers, 'driver_id'))
            ->where('driver_vehicles.active', true)
            ->get();

        return ['drivers' => $drivers, 'vehicles' => $vehicles];
    }

    protected function calculateCosts($distance, $duration, $carTypeId)
    {
        if (is_array($carTypeId)) {
            $carTypes = CarType::selectRaw(
                'id, (base_fare  + ((distance_price * ?) / 1000) + ((duration_price * surge_factor * ?) / 60)) as costs'
                , [$distance, $duration])
                ->whereIn('id', $carTypeId)
                ->get();
            return $carTypes->keyBy('id')->toArray();
        }

        return CarType::selectRaw(
            '(base_fare  + ((distance_price * ?) / 1000) + ((duration_price * surge_factor * ?) / 60)) as costs'
            , [$distance, $duration])
            ->where('id', $carTypeId)->first()->costs;
    }

    protected function updateRequest($request, $args) 
    {
        $input = Arr::except($args, ['id', 'directive', 'cancelled_by', 'cancel_reason', 'distance', 'duration']);

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

        $drivers = Driver::selectRaw('id AS driver_id, full_name, phone, avatar,
            ST_Distance_Sphere(point(longitude, latitude), point(?, ?))
            as distance
            ', [$lng, $lat]
            )
            ->having('distance', '<=', $radius)
            ->where('cab_status', 'Online')
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

        /*$result = $request->history['searching']['result'];
        $prices = $this->calculateCosts($args['distance'], $args['duration'], Arr::pluck($result['vehicles'], 'car_type_id'));
        $vehicles = Arr::keyBy($result['vehicles'], 'car_type_id');

        foreach ($vehicles as $carTypeId => $vehicle) {
            $vehicle['price'] = $prices[$carTypeId]['costs'];
        }

        [$carTypeId, $vehicles] = Arr::divide($vehicles);
        $result['vehicles'] = $vehicles;

        $payload = [
            'summary' => [
                'distance' => $args['distance'],
                'duration' => $args['duration']
            ],
            'redirected' => [
                'at' => date("Y-m-d H:i:s"),
                'notes' => $notes,
                'result' => $result
            ]
        ];

        $input['d_lat'] = $args['d_lat'];
        $input['d_lng'] = $args['d_lng'];
        $input['history'] = array_merge($request->history, $payload);

        $request = $this->updateRequest($request, $input);
        $request['result'] = $result;

        return $request;*/