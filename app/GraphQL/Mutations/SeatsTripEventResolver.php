<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\Driver;
use App\SeatsTrip;
use App\SeatsTripEntry;
use App\SeatsTripEvent;
use App\SeatsTripBooking;
use Illuminate\Support\Str;
use App\Helpers\StaticMapUrl;
use App\SeatsTripTransaction;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use App\Events\SeatsTripStatusChanged;

class SeatsTripEventResolver
{
    public function startTrip($_, array $args)
    {
        $trip = $this->getTripById($args['trip_id']);

        if ($trip->log_id) 
            throw new CustomException('This Trip has already been started!');

        $logId = (string) Str::uuid();

        $this->initTripEvent($args, $logId);

        Driver::updateLocation($args['latitude'], $args['longitude']);

        $this->broadcastTripStatus($trip, ['status' => 'STARTED', 'log_id' => $logId]);

        $trip->update(['log_id' => $logId]);

        return $trip;
    }

    public function updateDriverLocation($_, array $args)
    {
        try {
            $input = [
                'log_id' => $args['log_id'],
                'latitude' => $args['latitude'],
                'longitude' => $args['longitude']
            ];
            SeatsTripEntry::create($input);
            return Driver::updateLocation($args['latitude'], $args['longitude']);
        } catch (\Exception $e) {
            //
        }
    }

    public function atStation($_, array $args)
    {
        $payload = array([
            'station_id' => $args['station_id'],
            'station_name' => $args['station_name'],
            'status' => 'at station',
            'at' => date("Y-m-d H:i:s"),
            'lat' => $args['latitude'],
            'lng' => $args['longitude']
        ]);
        
        return $this->updateEventPayload($args['log_id'], $payload);
    }

    public function pickUser($_, array $args)
    {
        return $this->flipPickupStatus($args, true);
    }

    public function dropUser($_, array $args)
    {
        DB::beginTransaction();
        try {
            
            $this->flipPickupStatus($args, false);
            $this->updateBookingStatus($args, 'COMPLETED');

            if ($args['paid'] > 0) 
                $this->createTransaction($args);

            if ($args['payable'] != $args['paid']) 
                $this->updateUserBalance($args);

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException('Could not drop off user!');
        }

        return true;
    }

    public function endTrip($_, array $args)
    {
        $trip = $this->getTripById($args['trip_id']);

        if (!$trip->log_id) 
            throw new CustomException('This trip has already been ended!');

        $logId = $trip->log_id;

        $trip->update(['log_id' => null]);

        return $this->closeTripEvent($args, $logId, $trip);
    }

    public function destroy($_, array $args)
    {
        return SeatsTripEvent::whereIn('log_id', $args['log_id'])
            ->delete();
    }

    protected function updateEventPayload($logId, $payload)
    {
        try {
            $event = SeatsTripEvent::findOrFail($logId);
    
            if (array_key_exists('payload', $event->content)) 
                $payload = array_merge($event->content['payload'], $payload);
                
            return $event->update(['content' => array_merge($event->content, ['payload' => $payload])]);
        } catch (\Exception $e) {
            //
        }
    }

    protected function getTripById($id)
    {
        try {
            return SeatsTrip::with('partner:id,name')
                ->with('driver:id,name')
                ->findOrFail($id);
        } catch (\Exception $e) {
            throw new CustomException('We could not able to find this trip!');
        }
    }

    protected function initTripEvent($args, $logId)
    {
        try {
            $input = [
                'trip_id' => $args['trip_id'],
                'log_id' => $logId,
                'content' => [ 
                    'started' => [
                        'at' => date("Y-m-d H:i:s"),
                        'lat' => $args['latitude'],
                        'lng' => $args['longitude']
                    ]
                ]
            ];
            SeatsTripEvent::create($input);
        } catch (\Exception $e) {
            //
        }
    }

    protected function closeTripEvent($args, $logId, $trip)
    {
        try {
            $event = SeatsTripEvent::findOrFail($logId);

            $locations = SeatsTripEntry::where('log_id', $logId);

            if ($locations->count()) {
                foreach($locations->get() as $loc) $path[] = $loc->latitude.','.$loc->longitude;
                $updatedData['map_url'] = StaticMapUrl::generatePath(implode('|', $path));
                $locations->delete();
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

    protected function flipPickupStatus(array $args, bool $status)
    {
        try {
            return SeatsTripBooking::where('user_id', $args['user_id'])
                ->where('trip_id', $args['trip_id'])
                ->where('date', date('Y-m-d'))
                ->update(['is_picked_up' => $status]);
        } catch (\Exception $e) {
            throw new CustomException('Could not flip status!');
        }
    }

    protected function createTransaction(array $args)
    {
        try {
            $input = collect($args)->except(['payable', 'directive'])->toArray();

            return SeatsTripTransaction::create($input);
        } catch (\Exception $e) {
            throw new CustomException('Could not create transaction!');
        }
    }

    protected function updateUserBalance(array $args)
    {
        try {
            $balance = $args['payable'] - $args['paid'];

            return User::where('id', $args['user_id'])
                ->decrement('wallet_balance', $balance);
        } catch (\Exception $e) {
            throw new CustomException('Could not create transaction!');
        }
    }

    protected function updateBookingStatus(array $args, String $status)
    {
        try {
            return SeatsTripBooking::where('user_id', $args['user_id'])
                ->where('trip_id', $args['trip_id'])
                ->where('date', date('Y-m-d'))
                ->update(['status' => $status]);
        } catch (\Exception $e) {
            throw new CustomException('Could not update status!');
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
                'id' => $trip->partner->id,
                'name' => $trip->partner->name,
                '__typename' => 'Partner'
            ],
            'driver' => [
                'id' => $trip->driver->id,
                'name' => $trip->driver->name,
                '__typename' => 'Driver'
            ],
            '__typename' => 'SeatsTrip'
        ];
        broadcast(new SeatsTripStatusChanged($data));
    }

}
