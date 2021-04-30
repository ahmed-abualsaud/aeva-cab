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
use Illuminate\Support\Facades\Cache;
use App\Events\BusinessTripStatusChanged;
use App\Traits\HandleBusinessTripUserStatus;

class BusinessTripEventResolver
{
    use HandleDeviceTokens;
    use HandleBusinessTripUserStatus;

    public function startTrip($_, array $args)
    {
        $trip = $this->getTripById($args['trip_id']);

        if ($trip->status) throw new CustomException('This Trip has already been started!');

        $logId = (string) Str::uuid();

        $this->initTripEvent($args, $logId);

        $this->checkAbsence($args['trip_id']);

        $this->checkSchedule($args['trip_id']);

        Driver::updateLocation($args['latitude'], $args['longitude']);

        SendPushNotification::dispatch(
            $this->tripUsersToken($trip->id), 
            'has been started', 
            $trip->name,
            ['view' => 'BusinessTrip', 'id' => $args['trip_id']]
        );

        $this->broadcastTripStatus($trip, ['status' => 'STARTED', 'log_id' => $logId]);

        $trip->update(['log_id' => $logId, 'status' => true]);

        return $trip;
    }

    public function nearYou($_, array $args)
    {
        try { 
            SendPushNotification::dispatch(
                $this->stationUsersToken($args['station_id']), 
                'Qruz captain is so close to you',
                $args['trip_name'],
                ['view' => 'BusinessTrip', 'id' => $args['trip_id']]
            );

            $payload = array([
                'station_id' => $args['station_id'],
                'station_name' => $args['station_name'],
                'status' => 'nearby',
                'at' => date("Y-m-d H:i:s"),
                'lat' => $args['latitude'],
                'lng' => $args['longitude']
            ]);
            $this->updateEventPayload($args['log_id'], $payload);

        } catch (\Exception $e) {
            throw new CustomException("We could not able to notify selected station's users!");
        }

        return "Selected station's users have been notified!";
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
        $this->updateEventPayload($args['log_id'], $data);

        return "Pick up status has been changed successfully";
    }

    public function changeBusinessTripAttendanceStatus($_, array $args)
    {
        BusinessTripAttendance::updateOrCreate(
            ['date' => date('Y-m-d'), 'trip_id' => $args['trip_id'], 'user_id' => $args['user_id']], 
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
        
        $this->updateEventPayload($args['log_id'], $payload);

        Cache::tags('userTrips:'.$args['user_id'])->flush();

        return "Attendance status has been changed successfully";
    }

    public function pickUsers($_, array $args)
    {
        $msg = 'Welcome! May you be happy and safe throughout this trip.';
        $this->pickOrDropUsers($args, true, $msg);

        return $msg;
    }

    public function dropUsers($_, array $args)
    {
        $msg = 'Bye! We can\'t wait to see you next time.';
        $this->pickOrDropUsers($args, false, $msg);

        return $msg;
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
            BusinessTripEntry::create($input);
        } catch (\Exception $e) {
            //
        }

        return "Location has been updated";
    }

    public function endTrip($_, array $args)
    {
        $trip = $this->getTripById($args['trip_id']);

        if (!$trip->status) throw new CustomException('This trip has already been ended!');

        $logId = $trip->log_id;

        $trip->update(['log_id' => null, 'status' => false]);

        $this->updateUserStatus(
            $args['trip_id'],
            ['is_picked_up' => false, 'is_absent' => false, 'is_scheduled' => true]
        );

        $this->closeTripEvent($args, $logId, $trip);

        return 'Trip has been ended.';
    }

    public function destroy($_, array $args)
    {
        return BusinessTripEvent::whereIn('log_id', $args['log_id'])->delete();
    }

    protected function getTripById($id)
    {
        try {
            return BusinessTrip::with('partner:id,name')
                ->with('driver:id,name')
                ->findOrFail($id);
        } catch (\Exception $e) {
            throw new CustomException('We could not able to find this trip!');
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

            $this->updateEventPayload($args['log_id'], $data);
        } catch (\Exception $e) {
            throw new CustomException('We could not able to change selected users status!');
        }
    }

    protected function updateEventPayload($logId, $payload)
    {
        try {
            $event = BusinessTripEvent::findOrFail($logId);
    
            if (array_key_exists('payload', $event->content)) 
                $payload = array_merge($event->content['payload'], $payload);
                
            $event->update(['content' => array_merge($event->content, ['payload' => $payload])]);
        } catch (\Exception $e) {
            //
        }
    }

    protected function closeTripEvent($args, $logId, $trip)
    {
        try {
            $event = BusinessTripEvent::findOrFail($logId);

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

            $event->update($updatedData);
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
                    $msg = $args['user_name'] . ' has changed his attendance status to '.$status_text;
                    break;
                default:
                    $token = $this->userToken($args['user_id']);
                    $msg = 'The trip captain has changed your attendance status to '.$status_text.', If this isn\'t the case, you could revert it back from inside the trip.';
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
                'id' => $trip->partner->id,
                'name' => $trip->partner->name,
                '__typename' => 'Partner'
            ],
            'driver' => [
                'id' => $trip->driver->id,
                'name' => $trip->driver->name,
                '__typename' => 'Driver'
            ],
            '__typename' => 'BusinessTrip'
        ];
        broadcast(new BusinessTripStatusChanged($data));
    }
}
