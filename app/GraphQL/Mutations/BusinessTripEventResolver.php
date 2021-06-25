<?php

namespace App\GraphQL\Mutations;

use App\Driver;
use App\BusinessTrip;
use App\BusinessTripEntry;
use App\BusinessTripEvent;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\BusinessTripSchedule;
use App\Helpers\StaticMapUrl;
use App\BusinessTripAttendance;
use App\Jobs\SendPushNotification;
use App\Traits\HandleDeviceTokens;
use App\Exceptions\CustomException;
use App\Events\BusinessTripStatusChanged;
use App\Traits\HandleBusinessTripUserStatus;

class BusinessTripEventResolver
{
    use HandleDeviceTokens;
    use HandleBusinessTripUserStatus;

    public function startTrip($_, array $args)
    {
        $trip = $this->getTripById($args['trip_id']);

        if ($trip->log_id) 
            throw new CustomException('This Trip has already been started!');

        $logId = (string) Str::uuid();

        $this->initTripEvent($args, $logId, $trip->driver_id);

        $this->checkAbsence($args['trip_id']);

        $this->checkSchedule($args['trip_id']);

        Driver::updateLocation($args['latitude'], $args['longitude']);

        $this->broadcastTripStatus($trip, ['status' => 'STARTED', 'log_id' => $logId]);

        $trip->update(['log_id' => $logId, 'starts_at' => $args['trip_time']]);

        return $trip;
    }

    public function atStation($_, array $args)
    {
        try { 
            SendPushNotification::dispatch(
                $this->stationUsersToken($args['station_id']), 
                'Qruz captain has arrived at your station and will leave after 1 min',
                $args['trip_name'],
                ['view' => 'BusinessTrip', 'id' => $args['trip_id']]
            );

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

        } catch (\Exception $e) {
            throw new CustomException("We could not able to notify selected station's users!");
        }
    }

    public function changeBusinessTripPickupStatus($_, array $args)
    {
        $this->updateUserStatus(
            $args['trip_id'], ['is_picked_up' => $args['is_picked_up']], $args['user_id']
        );

        $data = array([
            'user_id' => $args['user_id'],
            'user_name' => $args['user_name'],
            'status' => $args['is_picked_up'] ? 'picked up' : 'dropped off',
            'at' => date("Y-m-d H:i:s"),
            'lat' => $args['latitude'],
            'lng' => $args['longitude'],
            'by' => 'user'
        ]);
        
        return $this->updateEventPayload($args['log_id'], $data);
    }

    public function changeBusinessTripAttendanceStatus($_, array $args)
    {
        BusinessTripAttendance::updateOrCreate(
            ['date' => $args['date'], 'trip_id' => $args['trip_id'], 'user_id' => $args['user_id']], 
            ['is_absent' => $args['is_absent']]
        );

        $this->updateUserStatus(
            $args['trip_id'], ['is_absent' => $args['is_absent']], $args['user_id']
        );

        $this->attendanceNotification($args);

        $payload = array([
            'user_id' => $args['user_id'],
            'user_name' => $args['user_name'],
            'status' => $args['is_absent'] ? 'absent' : 'present',
            'at' => date("Y-m-d H:i:s"),
            'lat' => $args['latitude'],
            'lng' => $args['longitude'],
            'by' => $args['by']
        ]);
        
        return $this->updateEventPayload($args['log_id'], $payload);
    }

    public function pickUsers($_, array $args)
    {
        $msg = 'Welcome! May you be happy and safe throughout this trip.';

        return $this->pickOrDropUsers($args, true, $msg);
    }

    public function dropUsers($_, array $args)
    {
        $msg = 'Bye! We can\'t wait to see you next time.';
        
        return $this->pickOrDropUsers($args, false, $msg);
    }

    public function updateDriverLocation($_, array $args)
    {
        try {
            $input = [
                'log_id' => $args['log_id'],
                'latitude' => $args['latitude'],
                'longitude' => $args['longitude']
            ];
            Driver::updateLocation($args['latitude'], $args['longitude']);
            return BusinessTripEntry::create($input);
        } catch (\Exception $e) {
            //
        }
    }

    public function endTrip($_, array $args)
    {
        $trip = $this->getTripById($args['trip_id']);

        if (!$trip->log_id) 
            throw new CustomException('This trip has already been ended!');

        $logId = $trip->log_id;

        $trip->update(['log_id' => null, 'starts_at' => null]);

        $this->updateUserStatus(
            $args['trip_id'],
            ['is_picked_up' => false, 'is_absent' => false, 'is_scheduled' => true]
        );

        return $this->closeTripEvent($args, $logId, $trip);
    }

    public function destroy($_, array $args)
    {
        return BusinessTripEvent::whereIn('log_id', $args['log_id'])->delete();
    }

    protected function getTripById($id)
    {
        try {
            return BusinessTrip::select(
                'business_trips.id', 'business_trips.name', 
                'business_trips.log_id', 'business_trips.type',
                'drivers.id as driver_id', 'drivers.name as driver_name',
                'partners.id as partner_id', 'partners.name as partner_name'
            )
            ->join('drivers', 'drivers.id', '=', 'business_trips.driver_id')
            ->join('partners', 'partners.id', '=', 'business_trips.partner_id')
            ->findOrFail($id);
        } catch (\Exception $e) {
            throw new CustomException('Could not find this trip!');
        }
    }

    protected function pickOrDropUsers($args, $is_picked_up, $msg)
    {
        try {
            $user_ids = Arr::pluck($args['users'], 'id');

            $this->updateUserStatus(
                $args['trip_id'], ['is_picked_up' => $is_picked_up], $user_ids
            );

            SendPushNotification::dispatch(
                $this->usersToken($user_ids), 
                $msg, 
                $args['trip_name'],
                ['view' => 'BusinessTripUserStatus', 'id' => $args['trip_id']]
            );

            $payload = [
                'status' => $is_picked_up ? 'picked up' : 'dropped off',
                'at' => date("Y-m-d H:i:s"),
                'lat' => $args['latitude'], 
                'lng' => $args['longitude'],
                'by' => 'driver'
            ];

            foreach($args['users'] as $user) {
                $payload['user_id'] = $user['id'];
                $payload['user_name'] = $user['name'];
                $data[] = $payload;
            }

            return $this->updateEventPayload($args['log_id'], $data);

        } catch (\Exception $e) {
            throw new CustomException('Could not change selected users status!');
        }
    }

    protected function updateEventPayload($logId, $payload)
    {
        try {
            $event = BusinessTripEvent::select('content', 'log_id')
                ->findOrFail($logId);
    
            if (array_key_exists('payload', $event->content)) 
                $payload = array_merge($event->content['payload'], $payload);
                
            return $event->update(['content' => array_merge($event->content, ['payload' => $payload])]);
        } catch (\Exception $e) {
            //
        }
    }

    protected function closeTripEvent($args, $logId, $trip)
    {
        try {
            $event = BusinessTripEvent::select('content', 'log_id')
                ->findOrFail($logId);

            $locations = BusinessTripEntry::select('latitude', 'longitude')
                ->where('log_id', $logId)
                ->get();

            if ($locations->isNotEmpty()) {
                foreach($locations as $loc) 
                    $path[] = $loc->latitude.','.$loc->longitude;

                $updatedData['map_url'] = StaticMapUrl::generatePath(implode('|', $path));

                BusinessTripEntry::where('log_id', $logId)
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

    protected function attendanceNotification($args)
    {
        try {
            $status_text = $args['is_absent'] ? 'Absent' : 'Present';

            switch($args['by']) {
                case 'user':
                    $token = $this->driverToken($args['driver_id']);
                    $msg = $args['user_name'] . ' has changed the attendance status to '.$status_text;
                    break;
                default:
                    $token = $this->userToken($args['user_id']);
                    $msg = 'Trip captain has changed your attendance status to '.$status_text.', If this isn\'t the case, you could revert it back from inside the trip.';
            }

            SendPushNotification::dispatch(
                $token, 
                $msg, 
                $args['trip_name'],
                ['view' => 'BusinessTripUserStatus', 'id' => $args['trip_id']]
            );

        } catch(\Exception $e) {
            //
        }
    }

    protected function checkAbsence($trip_id)
    {
        try {
            $absent_users = BusinessTripAttendance::whereAbsent($trip_id);

            if ($absent_users) 
                $this->updateUserStatus($trip_id, ['is_absent' => true], $absent_users);
        } catch(\Exception $e) {
            //
        }
    }

    protected function checkSchedule($trip_id)
    {
        try {
            $not_scheduled_users = BusinessTripSchedule::whereNotScheduled($trip_id);

            if ($not_scheduled_users) 
                $this->updateUserStatus($trip_id, ['is_scheduled' => false], $not_scheduled_users);
        } catch(\Exception $e) {
            //
        }
    }

    protected function initTripEvent($args, $logId, $driverId)
    {
        try {
            $input = [
                'trip_id' => $args['trip_id'],
                'driver_id' => $driverId,
                'trip_time' => $args['trip_time'],
                'log_id' => $logId,
                'content' => [ 
                    'started' => [
                        'at' => date("Y-m-d H:i:s"),
                        'lat' => $args['latitude'],
                        'lng' => $args['longitude']
                    ]
                ]
            ];
            BusinessTripEvent::create($input);
        } catch (\Exception $e) {
            //
        }
    }

    protected function broadcastTripStatus($trip, $input)
    {
        $data = [
            'id' => $trip->id,
            'log_id' => $input['log_id'],
            'name' => $trip->name,
            'status' => $input['status'],
            'type' => $trip->type,
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
            '__typename' => 'BusinessTrip'
        ];
        broadcast(new BusinessTripStatusChanged($data));
    }
}
