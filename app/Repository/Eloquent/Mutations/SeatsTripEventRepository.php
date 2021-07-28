<?php

namespace App\Repository\Eloquent\Mutations;

use App\User;
use App\Driver;
use App\SeatsTrip;
use App\SeatsTripEntry;
use App\SeatsTripEvent;
use App\SeatsTripBooking;
use Illuminate\Support\Str;
use App\Helpers\StaticMapUrl;
use App\SeatsTripAppTransaction;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use App\Events\SeatsTripStatusChanged;
use App\Repository\Eloquent\BaseRepository;
use App\Repository\Mutations\SeatsTripEventRepositoryInterface;

class SeatsTripEventRepository extends BaseRepository implements SeatsTripEventRepositoryInterface
{
    public function __construct(SeatsTripEvent $model)
    {
        parent::__construct($model);
    }

    public function changeDriverStatus(array $args)
    {
        $trip = $this->getTripById($args['trip_id']);

        if ($trip->log_id) 
            throw new CustomException(__('lang.trip_already_started'));

        $logId = (string) Str::uuid();

        $this->initTripEvent($args, $logId, $trip->driver_id, $trip->vehicle_id);

        $trip->update(['log_id' => $logId, 'ready_at' => date("Y-m-d H:i:s")]);

        return $trip;
    }

    public function startTrip(array $args)
    {
        $trip = $this->getTripById($args['trip_id']);

        $payload = [
            'started' => [
                'at' => date("Y-m-d H:i:s"),
                'lat' => $args['latitude'],
                'lng' => $args['longitude']
            ]
        ];

        $event = $this->model->select('content', 'log_id')->findOrFail($trip['log_id']);

        $event->update(['content' => array_merge($event->content, $payload)]);

        $trip->update(['starts_at' => $args['trip_time']]);

        Driver::updateLocation($args['latitude'], $args['longitude']);

        $this->broadcastTripStatus($trip, ['status' => 'STARTED', 'log_id' => $trip['log_id']]);

        return $trip;
    }

    public function updateDriverLocation(array $args)
    {
        try {
            $input = [
                'log_id' => $args['log_id'],
                'latitude' => $args['latitude'],
                'longitude' => $args['longitude']
            ];
            Driver::updateLocation($args['latitude'], $args['longitude']);
            return SeatsTripEntry::create($input);
        } catch (\Exception $e) {
            //
        }
    }

    public function atStation(array $args)
    {
        $payload = array([
            'station_id' => $args['station_id'],
            'station_name' => $args['station_name'],
            'status' => 'at station',
            'at' => date("Y-m-d H:i:s"),
            'eta' => $args['eta'],
            'lat' => $args['latitude'],
            'lng' => $args['longitude']
        ]);
        
        return $this->updateEventPayload($args['log_id'], $payload);
    }

    public function pickUser(array $args)
    {
        return $this->updateBooking($args, ['is_picked_up' => true]);
    }

    public function dropUser(array $args)
    {
        DB::beginTransaction();
        try {
            
            $this->updateBooking($args, ['is_picked_up' => false, 'status' => 'COMPLETED']);

            $this->createTransaction($args);

            if ($args['payable'] != $args['paid']) {
                $balance = $args['payable'] - $args['paid'];
                User::updateWallet($args['user_id'], $balance);
            }

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException(__('lang.drop_user_failed'));
        }

        return true;
    }

    public function endTrip(array $args)
    {
        $trip = $this->getTripById($args['trip_id']);

        if (!$trip->log_id) 
            throw new CustomException(__('lang.trip_ended'));

        $logId = $trip->log_id;

        $trip->update(['log_id' => null, 'starts_at' => null, 'ready_at' => null]);

        return $this->closeTripEvent($args, $logId, $trip);
    }

    public function destroy(array $args)
    {
        return $this->model->whereIn('log_id', $args['log_id'])
            ->delete();
    }

    protected function updateEventPayload($logId, $payload)
    {
        try {
            $event = $this->model->select('content', 'log_id')
                ->findOrFail($logId);
    
            if (array_key_exists('payload', $event->content)) 
                $payload = array_merge($event->content['payload'], $payload);
                
            return $event->update(['content' => array_merge($event->content, ['payload' => $payload])]);
        } catch (\Exception $e) {
            //
        }
    }

    protected function initTripEvent($args, $logId, $driverId, $vehicleId)
    {
        try {
            $input = [
                'trip_id' => $args['trip_id'],
                'driver_id' => $driverId,
                'vehicle_id' => $vehicleId,
                'trip_time' => $args['trip_time'],
                'log_id' => $logId,
                'content' => [ 
                    'ready' => [
                        'at' => date("Y-m-d H:i:s"),
                        'lat' => $args['latitude'],
                        'lng' => $args['longitude']
                    ]
                ]
            ];
            $this->model->create($input);
        } catch (\Exception $e) {
            //
        }
    }

    protected function closeTripEvent($args, $logId, $trip)
    {
        try {
            $event = $this->model->select('content', 'log_id')
                ->findOrFail($logId);

            $locations = SeatsTripEntry::select('latitude', 'longitude')
                ->where('log_id', $logId)
                ->get();

            if ($locations->isNotEmpty()) {
                foreach($locations as $loc) 
                    $path[] = $loc->latitude.','.$loc->longitude;

                $updatedData['map_url'] = StaticMapUrl::generatePath(implode('|', $path));

                SeatsTripEntry::where('log_id', $logId)
                    ->delete();
            }

            $ended = ['at' => date("Y-m-d H:i:s")];

            if (array_key_exists('latitude', $args) && array_key_exists('longitude', $args)) {
                $ended['lat'] = $args['latitude'];
                $ended['lng'] = $args['longitude'];

                $this->broadcastTripStatus($trip, ['status' => 'ENDED', 'log_id' => null]);
            }

            $updatedData['content'] = array_merge($event->content, ['ended' => $ended]);

            return $event->update($updatedData);
        } catch (\Exception $e) {
            //
        }
    }

    protected function createTransaction(array $args)
    {
        try {
            $input = collect($args)->except(['directive', 'payable', 'paid', 'log_id'])->toArray();
            $input['amount'] = $args['paid'];
            return SeatsTripAppTransaction::create($input);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.create_trnx_failed'));
        }
    }

    protected function updateBooking(array $args, array $data)
    {
        try {
            return SeatsTripBooking::where('id', $args['booking_id'])
                ->update($data);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.update_booking_failed'));
        }
    }

    protected function getTripById($id)
    {
        try {
            return SeatsTrip::select(
                'seats_trips.id', 'seats_trips.name', 'seats_trips.log_id',
                'drivers.id as driver_id', 'drivers.name as driver_name',
                'partners.id as partner_id', 'partners.name as partner_name',
                'vehicles.id as vehicle_id'
            )
            ->join('drivers', 'drivers.id', '=', 'seats_trips.driver_id')
            ->join('partners', 'partners.id', '=', 'seats_trips.partner_id')
            ->join('vehicles', 'vehicles.id', '=', 'seats_trips.vehicle_id')
            ->findOrFail($id);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.trip_not_found'));
        }
    }

    protected function broadcastTripStatus($trip, $input)
    {
        $data = [
            'id' => $trip->id,
            'log_id' => $input['log_id'],
            'name' => $trip->name,
            'status' => $input['status'],
            'partner' => [
                'id' => $trip->partner_id,
                'name' => $trip->partner_name,
                '__typename' => 'Partner'
            ],
            'driver' => [
                'id' => $trip->driver_id,
                'name' => $trip->driver_name,
                '__typename' => 'Driver'
            ],
            '__typename' => 'SeatsTrip'
        ];

        broadcast(new SeatsTripStatusChanged($data));
    }
}
