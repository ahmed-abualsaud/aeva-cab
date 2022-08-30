<?php

namespace Aeva\Cab\Domain\Repository\Eloquent\Mutations;

use App\Helpers\TraceEvents;
use App\User;
use App\Driver;
use App\PromoCode;
use App\DriverLog;
use App\DriverStats;
use App\PromoCodeUsage;

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

        $route = $this->calculateEstimatedRoute($args['s_lat'], $args['s_lng'], $args['d_lat'], $args['d_lng']);

        $args['distance'] = $route['distance'];
        $args['duration'] = $route['duration'];
        $result = $this->getNearestDriversWithVehicles($args);

        $payload = [
            'summary' => $route,
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

        if ($request->status == 'Searching') {
            $dialog_shown = (time() - strtotime($request->history['searching']['at'])) < $this->settings('Show Acceptance Dialog');
        }

        if ($request->status == 'Sending') {
            $dialog_shown = (time() - strtotime($request->history['sending']['at'])) < $this->settings('Show Acceptance Dialog');
        }

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
        if ($request->status == 'Searching') {
            $input['costs'] = $filtered[0]['price'];
            $input['remaining'] = $input['costs'];
        }

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

        if ((array_key_exists('s_lat', $args) || array_key_exists('s_lng', $args)) &&
        ($args['s_lat'] != $request->s_lat || $args['s_lng'] != $request->s_lng)) {
            $input['s_lat'] = $args['s_lat'];
            $input['s_lng'] = $args['s_lng'];
            $route = $this->calculateEstimatedRoute($args['s_lat'], $args['s_lng'], $request->d_lat, $request->d_lng);
            $payload['summary'] = $route;
            $input['costs'] = $this->calculateCosts($route['distance'], $route['duration'], $filtered[0]['car_type_id']);
            $input['remaining'] = $input['costs'];
        }

        $driversIds = Arr::pluck($filtered, 'driver_id');

        if ($request->status == 'Sending') {
            DriverStats::whereIn('driver_id', $driversIds)->increment('missed_cab_requests', 1);
            DriverLog::log(['driver_id' => $driversIds, 'missed_cab_requests' => 1]);
        }

        $input['status'] = 'Sending';
        $input['history'] = array_merge($request->history, $payload);

        $request = $this->updateRequest($request, $input);

        DriverStats::whereIn('driver_id', $driversIds)->increment('received_cab_requests', 1);
        DriverLog::log(['driver_id' => $driversIds, 'received_cab_requests' => 1]);
        multiple_trace(TraceEvents::RECEIVED_CAB_REQUEST,$request->id,new Driver(),$driversIds);

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
        trace(TraceEvents::ACCEPT_CAB_REQUEST,$request->id);

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
        trace(TraceEvents::ARRIVED_CAB_REQUEST,$request->id);
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
        trace(TraceEvents::START_CAB_REQUEST,$request->id);
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

        // $distance = 0;
        // $last_location = CabRequestEntry::getLastLocation($args['id']);
        // if (array_key_exists('locations', $args) && is_array($args['locations']) && !empty($args['locations']))
        // {
        //     $locations = [];
        //     if ($last_location) {
        //         $locations = explode('|', $last_location->path);
        //     }

        //     if (count($args['locations']) >= count($locations)) {
        //         $locations = $args['locations'];
        //     }

        //     if(gettype($locations[0]) == 'string') {
        //         $locations = $last_location->path;
        //     }

        //     $distance = $this->calculateRealRoute($request->s_lat, $request->s_lng, $request->d_lat, $request->d_lng, $locations)['distance'];
        // }
        // else {
        //     if($last_location && empty($distance)) { $distance = $last_location->distance; }
        // }

        $duration = (time() - strtotime($request->history['started']['at']));
        $distance = $request->history['summary']['distance'];

        $payload = [
            'summary' => [
                'distance' => $distance,
                'duration' => $duration
            ],
            'ended' => [
                'at' => date("Y-m-d H:i:s"),
            ]
        ];

        trace(TraceEvents::END_CAB_REQUEST,$request->id);

        $vehicles = $request->history['searching']['result']['vehicles'];
        $vehicle = Arr::where($vehicles, function ($value, $key) use ($request){
            return $value['driver_id'] == $request->driver_id;
        });

        $vehicle = array_values($vehicle);

        $input['id'] = $args['id'];
        $input['status'] = 'Ended';
        $input['costs'] = $this->calculateCosts($distance, $duration, $vehicle[0]['car_type_id'], $request->history['started']['waiting_time']);
        $input['remaining'] = $input['costs'];
        $input['history'] = array_merge($request->history, $payload);
        $input['map_url'] = CabRequestEntry::buildMapURL($args['id']);

        $this->addReferralBonus($request->driver_id);

        if ($request->promo_code_id) {
            $promoCode = PromoCode::find($request->promo_code_id);
            $discount_rate = ($input['costs'] * $promoCode->percentage / 100);

            if ($discount_rate > $promoCode->max_discount) {
                $discount_rate = $promoCode->max_discount;
            }
            $input['remaining'] = ceil($input['costs'] - $discount_rate);

            PromoCodeUsage::where('user_id', $request->user_id)
                ->where('promo_code_id', $request->promo_code_id)
                ->update(['used' => true]);
        }

        $request = $this->updateRequest($request, $input);

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
            $vehicles = $request->history['searching']['result']['vehicles'];
            $car_type = $request->history['sending']['chosen_car_type'];
            $dialog_shown = (time() - strtotime(@$request->history['sending']['at']) ?? $request->history['searching']['at']) < $this->settings('Show Acceptance Dialog');

            $filtered = Arr::where($vehicles, function ($value, $key) use ($args, $car_type){
                return $value['car_type'] == $car_type;
            });

            $filtered = array_values($filtered);
            $drivers_ids = Arr::pluck($filtered, 'driver_id');
            ! $dialog_shown and multiple_trace(TraceEvents::MISSED_CAB_REQUEST,$request->id,new Driver(),$drivers_ids);
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

            if ($request->promo_code_id) {
                PromoCodeUsage::where('user_id', $request->user_id)
                    ->where('promo_code_id', $request->promo_code_id)
                    ->update(['used' => true]);
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
                trace(TraceEvents::CANCEL_CAB_REQUEST,$request->id);
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
        $result = $request->history['searching']['result'];
        $waiting_time = strtotime($request->history['started']['at']) - strtotime($request->history['arrived']['at']);
        $duration = time() - strtotime($request->history['started']['at']);
        $distance = $this->calculateEstimatedRoute($request->s_lat, $request->s_lng, $args['s_lat'], $args['s_lng'])['distance'];
        $route = $this->calculateEstimatedRoute($args['s_lat'], $args['s_lng'], $args['d_lat'], $args['d_lng']);

        foreach ($result['vehicles'] as $vehicle) {
            if ($vehicle['car_type'] == $request->history['sending']['chosen_car_type']) {
                $chosen_vehicle = $vehicle;
                break;
            }
        }

        $costs1 = $this->calculateCosts($distance, $duration, $chosen_vehicle['car_type_id'], $waiting_time);
        $costs2 = $this->calculateCosts($route['distance'], $route['duration'], $chosen_vehicle['car_type_id'], $waiting_time);

        $payload = [
            'summary' => [
                'distance' => $route['distance'] + $distance,
                'duration' => $route['duration'] + $duration
            ],
            'searching' => [
                'at' => $request->history['searching']['at'],
                'user' => $request->history['searching']['user'],
                'result' => $result
            ],
            'redirect' => [
                'previous_route_costs' => $costs1,
                'next_route_costs' => $costs2,
                'at' => date("Y-m-d H:i:s"),
                's_lat' => $args['s_lat'],
                's_lng' => $args['s_lng'],
                's_address' => array_key_exists('s_address', $args) ? $args['s_address'] : "",
                'd_lat' => $args['d_lat'],
                'd_lng' => $args['d_lng'],
                'd_address' => array_key_exists('d_address', $args) ? $args['d_address'] : ""
            ]
        ];

        $input['d_lat'] = $args['d_lat'];
        $input['d_lng'] = $args['d_lng'];
        $input['d_address'] = $args['d_address'];
        $input['costs'] = $costs1 + $costs2;
        $input['remaining'] = $input['costs'];
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

    public function updateDriverCabStatus(array $args)
    {
        $active_requests = $this->model->driverLive($args)->first();
        if($active_requests && in_array($args['cab_status'], ['Offline', 'Online'])) {
            throw new CustomException(__('lang.update_status_failed').' id = '.$active_requests->id);
        }
        return $this->updateDriverStatus($args['driver_id'], $args['cab_status']);
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
