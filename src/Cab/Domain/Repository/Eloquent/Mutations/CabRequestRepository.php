<?php

namespace Aeva\Cab\Domain\Repository\Eloquent\Mutations;   

use App\User;
use App\Driver;
use App\DriverLog;
use App\DriverStats;

use App\Jobs\SendPushNotification;
use App\Exceptions\CustomException;

use App\Traits\HandleDriverAttributes;

use Aeva\Cab\Domain\Models\CabRating;
use Aeva\Cab\Domain\Models\CabRequest;
use Aeva\Cab\Domain\Models\CabRequestEntry;

use Aeva\Cab\Domain\Traits\CabRequestHelper;
use Aeva\Cab\Domain\Traits\HandleDeviceTokens;

use Aeva\Cab\Domain\Events\AcceptCabRequest;
use Aeva\Cab\Domain\Events\DismissCabRequest;
use Aeva\Cab\Domain\Events\CabRequestCancelled;
use Aeva\Cab\Domain\Events\CabRequestStatusChanged;

use Aeva\Cab\Domain\Repository\Eloquent\BaseRepository;
use Aeva\Cab\Domain\Repository\Mutations\CabRequestRepositoryInterface;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;


class CabRequestRepository extends BaseRepository implements CabRequestRepositoryInterface
{
    use CabRequestHelper;
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
        $input = Arr::except($args, ['directive', 'distance', 'total_eta']);
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
                'user' => User::find($args['user_id'])
            ]
        ];

        $input['history'] = $payload;
        $input['status'] = 'Scheduled';
        $input['next_free_time'] = $args['next_free_time'];

        return $this->model->create($input); 
    }

    public function search(array $args)
    {
        $input = Arr::except($args, ['directive', 'distance', 'duration']);

        if ($args['user_id'] == 0 || is_null($args['user_id']) || !User::where('id', $args['user_id'])->exists()) {
            throw new CustomException(__('lang.user_not_found'));
        }

        $active_requests = $this->model->wherePending($args['user_id'])->first();

        if($active_requests) {
            throw new CustomException(__('lang.request_inprogress'));
        }

        $settings = $this->settings(['Coverage Radius', 'Coverage Center Latitude', 'Coverage Center Longitude']);
        $cov_rds = $settings['Coverage Radius'];
        $cov_lat = $settings['Coverage Center Latitude'];
        $cov_lng = $settings['Coverage Center Longitude'];
        $radius = $this->sphereDistance($args['s_lat'], $args['s_lng'], $cov_lat, $cov_lng);
        if( $radius > $cov_rds) {
            throw new CustomException(__('lang.out_of_coverage_area'));
        }

        $result = $this->getNearestDriversWithVehicles($args);

        $payload = [
            'summary' => [
                'distance' => $args['distance'],
                'duration' => $args['duration']
            ],
            'searching' => [
                'at' => date("Y-m-d H:i:s"),
                'user' => User::find($args['user_id']),
                'result' => $result
            ]
        ];

        $input['status'] = 'Searching';
        $input['history'] = $payload;

        $request = $this->model->create($input);
        $request['result'] = $result;

        return $request;
    }

    public function pickCarType(array $args)
    {
        $request = $this->findRequest($args['id']);

        if ( $request->status != 'Searching' ) {
            throw new CustomException(__('lang.request_drivers_failed'));
        }

        $input['history'] = array_merge($request->history, [
            'sending' => [
                'chosen_car_type' => $args['car_type'],
            ]
        ]);
        $request = $this->updateRequest($request, $input);
        return $request;
    }

    public function send(array $args) 
    {
        $request = $this->findRequest($args['id']);

        $dialog_shown = (time() - strtotime($request->history['searching']['at'])) < $this->settings('Show Acceptance Dialog');

        if ( !in_array($request->status, ['Searching', 'Sending']) || ($request->status == 'Sending' && $dialog_shown) ) {
            throw new CustomException(__('lang.request_drivers_failed'));
        }

        if(array_key_exists('car_type', $args) && $args['car_type']) {
            $car_type = $args['car_type'];
        } else {
            $car_type = $request->history['sending']['chosen_car_type'];
        }

        $vehicles = $request->history['searching']['result']['vehicles'];

        $filtered = Arr::where($vehicles, function ($value, $key) use ($args, $car_type){
            return $value['car_type'] == $car_type;
        });

        if ( $filtered == null ) {
            throw new CustomException(__('lang.unavailable_car_type'));
        }

        $filtered = array_values($filtered);

        $payload = [
            'sending' => [
                'at' => date("Y-m-d H:i:s"),
                'chosen_car_type' => $car_type,
                'payment_method' => $args['payment_method']
            ],
            'missing' => ($request->status == 'Sending')?  [
                'status' => true, 
                'missed' => $this->getMissedDrivers($request, Arr::pluck($filtered, 'driver_id'))
            ] : [    
                'status' => false,
                'missed' => []
            ]
        ];
        
        $input['status'] = 'Sending';
        $input['costs'] = $filtered[0]['price'];
        $input['history'] = array_merge($request->history, $payload);
        
        if ((array_key_exists('s_lat', $args) || array_key_exists('s_lng', $args)) && 
            ($args['s_lat'] != $request->s_lat || $args['s_lng'] != $request->s_lng)) {
                $input['costs'] = $this->calculateCosts($args['distance'], $args['duration'], $filtered[0]['car_type_id']);
        }

        $request = $this->updateRequest($request, $input);

        $driversIds = Arr::pluck($filtered, 'driver_id');

        DriverStats::whereIn('driver_id', $driversIds)->increment('received_cab_requests', 1);
        DriverLog::log(['driver_id' => $driversIds, 'received_cab_requests' => 1]);

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

        if ( $request->status == 'Accepted' && $request->driver_id ) {
            throw new CustomException(__('lang.request_already_accepted_by_another_driver'));
        }

        DriverStats::where('driver_id', $args['driver_id'])->increment('accepted_cab_requests', 1);
        DriverLog::log(['driver_id' => $args['driver_id'], 'accepted_cab_requests' => 1]);

        $vehicles = $request->history['searching']['result']['vehicles'];

        if ( !array_key_exists('vehicle_id', $args) || $args['vehicle_id'] == null ) {
            $vehicle = Arr::where($vehicles, function ($value, $key) use ($args){
                return $value['driver_id'] == $args['driver_id'];
            });
        } else {
            $vehicle = Arr::where($vehicles, function ($value, $key) use ($args){
                return $value['vehicle_id'] == $args['vehicle_id'];
            });
        }

        $vehicle = array_values($vehicle);
        $args['vehicle_id'] = $vehicle[0]['vehicle_id'];

        $payload = [
            'accepted' => [
                'at' => date("Y-m-d H:i:s"),
                'driver' => Driver::with('stats')->find($args['driver_id']),
                'vehicle' => $vehicle[0]
            ]
        ];

        $args['status'] = 'Accepted';
        $args['history'] = array_merge($request->history, $payload);

        $request = $this->updateRequest($request, $args);

        $this->updateDriverStatus($args['driver_id'], 'Riding');

        SendPushNotification::dispatch(
            $this->userToken($request->user_id),
            __('lang.request_accepted_body'),
            __('lang.request_accepted'),
            ['view' => 'RequestAccepted', 'id' => $args['id']]
        );

        broadcast(new CabRequestStatusChanged($request->toArray()));

        $drivers = $request->history['searching']['result']['drivers'];
        $drivers_ids = Arr::pluck($drivers, 'driver_id');

        $count = count($drivers_ids);
        for ($i = 0; $i < $count; $i++) {
            if ($drivers_ids[$i] == $args['driver_id']) {
                $drivers_ids[$i] = null;
                break;
            }
        }

        broadcast(new DismissCabRequest($drivers_ids));

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

        broadcast(new CabRequestStatusChanged($request->toArray()));

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
                'waiting_time' => (time() - strtotime($request->history['arrived']['at']))
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

        broadcast(new CabRequestStatusChanged($request->toArray()));

        return $request;
    }

    public function end(array $args)
    {
        $request = $this->findRequest($args['id']);
        
        if ( $request->status != 'Started') {
            throw new CustomException(__('lang.end_ride_failed'));
        }

        $distance = 0;
        $last_location =  CabRequestEntry::getLastLocation($args['id']);
        if($last_location) { $distance = $last_location->distance; }

        $duration = (time() - strtotime($request->history['started']['at']));

        $payload = [
            'summary' => [
                'distance' => $distance,
                'duration' => $duration
            ],
            'ended' => [
                'at' => date("Y-m-d H:i:s"),
            ]
        ];

        $vehicles = $request->history['searching']['result']['vehicles'];
        $vehicle = Arr::where($vehicles, function ($value, $key) use ($request){
            return $value['driver_id'] == $request->driver_id;
        });

        $vehicle = array_values($vehicle);

        $args['costs'] = $this->calculateCosts($distance, $duration, $vehicle[0]['car_type_id'], $request->history['started']['waiting_time']);

        $args['status'] = 'Ended';
        $args['history'] = array_merge($request->history, $payload);
        $args['map_url'] = CabRequestEntry::buildMapURL($args['id']);

        $request = $this->updateRequest($request, $args);

        $this->addReferralBonus($request->driver_id);

        SendPushNotification::dispatch(
            $this->userToken($request->user_id),
            __('lang.ride_ended_body'),
            __('lang.ride_ended'),
            ['view' => 'RideEnded', 'id' => $args['id']]
        );

        broadcast(new CabRequestStatusChanged($request->toArray()));

        return $request;
    }

    public function cancel(array $args)
    {
        $request = $this->findRequest($args['id']);
        $args['cancelled_by'] = strtolower($args['cancelled_by']);

        if ($request->status == 'Sending' && strtolower($args['cancelled_by']) == 'user') {
            $drivers = $request->history['searching']['result']['drivers'];
            $drivers_ids = Arr::pluck($drivers, 'driver_id');
            broadcast(new DismissCabRequest($drivers_ids));
        }

        if ( $request->status == 'Cancelled' ) {
            throw new CustomException(__('lang.request_already_cancelled'));
        }

        if ( in_array($request->status, ['Started', 'Ended', 'Completed']) ) {
            throw new CustomException(__('lang.cancel_cab_request_failed'));
        }

        $payload = [
            'cancelled' => [
                'at' => date("Y-m-d H:i:s"),
                'by' => $args['cancelled_by'],
                'reason' => array_key_exists('cancel_reason', $args) ? $args['cancel_reason'] : "Unknown"
            ]
        ];
        $args['history'] = array_merge($request->history, $payload);

        if($request->driver_id) {
            $this->updateDriverStatus($request->driver_id, 'Online');
        }
        $this->applyCancelFees($args['cancelled_by'], $request);

        $token = null;
        if (strtolower($args['cancelled_by']) == 'user') {
            if ($request->driver_id) {
                DriverStats::where('driver_id', $request->driver_id)->update([
                    'accepted_cab_requests' => DB::raw('accepted_cab_requests - 1'),
                ]);
                DriverLog::log([
                    'driver_id' => $request->driver_id, 
                    'accepted_cab_requests' => -1
                ]);
            }
            $args['status'] = 'Cancelled';
            $request = $this->updateRequest($request, $args);
            if ($request->driver_id) {
                $token = $this->driversToken($request->driver_id);
            }
        }

        if (strtolower($args['cancelled_by']) == 'driver') {
            if ($request->driver_id) {
                DriverStats::where('driver_id', $request->driver_id)->update([
                    'accepted_cab_requests' => DB::raw('accepted_cab_requests - 1'), 
                    'cancelled_cab_requests' => DB::raw('cancelled_cab_requests + 1')
                ]);
                DriverLog::log([
                    'driver_id' => $request->driver_id, 
                    'cancelled_cab_requests' => 1,
                    'accepted_cab_requests' => -1
                ]);
            }
            $request = $this->searchExistedRequest($args);
            $token = $this->userToken($request->user_id);
        }

        if ($token) {
            SendPushNotification::dispatch(
                $token,
                __('lang.request_cancelled_body'),
                __('lang.request_cancelled'),
                ['view' => 'CancelRequest', 'id' => $args['id']]
            );
        }

        $socketRequest = clone $request;
        $socketRequest->status = 'Cancelled';

        broadcast(new CabRequestCancelled($args['cancelled_by'], $socketRequest));
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

    public function updateDriverCabStatus(array $args)
    {
        $active_requests = $this->model->live($args)->where('driver_id', $args['driver_id'])->first();
        if($active_requests && in_array($args['cab_status'], ['Offline', 'Online'])) {
            throw new CustomException(__('lang.update_status_failed').' id = '.$active_requests->id);
        }
        return $this->updateDriverStatus($args['driver_id'], $args['cab_status']);
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
                'user' => $request->history['searching']['user'],
                'result' => $result
            ],
            're_search' => [
                'at' => date("Y-m-d H:i:s")
            ]
        ];

        $input['d_lat'] = $args['d_lat'];
        $input['d_lng'] = $args['d_lng'];
        $input['d_address'] = $args['d_address'];
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
                'user' => $request->history['searching']['user'],
                'result' => $result
            ],
            're_send' => [
                'at' => date("Y-m-d H:i:s")
            ]
        ];

        $filtered = array_values($filtered);

        $input['d_lat'] = $args['d_lat'];
        $input['d_lng'] = $args['d_lng'];
        $input['d_address'] = $args['d_address'];
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
        $waiting_time = ($action == 'start') ? (time() - strtotime($request->history['arrived']['at'])) : 0;
        $prices = $this->calculateCosts($args['distance'], $args['duration'], Arr::pluck($result['vehicles'], 'car_type_id'), $waiting_time);
        $vehicles = $result['vehicles'];

        foreach ($vehicles as $vehicle) {
            $vehicle['price'] = $prices[$vehicle['car_type_id']]['costs'];
        }

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
                'user' => $request->history['searching']['user'],
                'result' => $result
            ],
            're_'.$action => [
                'at' => date("Y-m-d H:i:s")
            ]
        ];

        $filtered = array_values($filtered);

        $input['d_lat'] = $args['d_lat'];
        $input['d_lng'] = $args['d_lng'];
        $input['d_address'] = $args['d_address'];
        $input['costs'] = ($action == 'start') ? $filtered[0]['price'] + $request->costs : $filtered[0]['price']; 
        $input['history'] = array_merge($request->history, $payload);

        $request = $this->updateRequest($request, $input);

        SendPushNotification::dispatch(
            $this->driversToken($request->driver_id),
            __('lang.ride_redirection_body'),
            __('lang.ride_redirection'),
            ['view' => 'RideRedirection', 'id' => $args['id']]
        );

        $socketRequest = $request->toArray();
        $socketRequest['status'] = 'Redirected';

        broadcast(new CabRequestStatusChanged($socketRequest));

        return $request;
    }

    protected function searchExistedRequest(array $args) 
    {
        $request = $this->findRequest($args['id']);

        if (in_array($request->status, ['Started', 'Ended', 'Completed', 'Cancelled'])) {
            throw new CustomException(__('lang.search_request_failed'));
        }

        $cancelled_drivers = array_key_exists('cancelled_drivers', $request->history['searching']) ? $request->history['searching']['cancelled_drivers'] : [];
        array_push($cancelled_drivers, $request->driver_id);

        $result = $this->getNearestDriversWithVehicles([
            's_lat' => $request->s_lat,
            's_lng' => $request->s_lng,
            'distance' => $request->history['summary']['distance'],
            'duration' => $request->history['summary']['duration'],
            'cancelled_drivers' => $cancelled_drivers
        ]);
    
        $payload = [
            'summary' => [
                'distance' => $request->history['summary']['distance'],
                'duration' => $request->history['summary']['duration']
            ],
            'searching' => [
                'at' => date("Y-m-d H:i:s"),
                'user' => $request->history['searching']['user'],
                'result' => $result,
                'cancelled_drivers' => $cancelled_drivers
            ]
        ];

        $input['status'] = 'Searching';
        $input['history'] = array_merge($request->history, $payload);
        $input['driver_id'] = null;

        $request = $this->updateRequest($request, $input);
        $request['result'] = $result;

        return $request;
    }
}