<?php

namespace App\GraphQL\Mutations;

use App\Driver;
use App\BusinessTrip;
use App\BusinessTripEntry;
use App\BusinessTripEvent;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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

        if ($trip->status) 
            throw new CustomException('This Trip has already been started!');

        $log_id = $trip->subscription_code.'-'.Str::random(4).'-'.uniqid();

        $this->initTripEvent($args, $log_id);

        $this->checkAbsence($args['trip_id']);

        Driver::updateLocation($args['latitude'], $args['longitude']);

        SendPushNotification::dispatch(
            $this->getBusinessTripUsersToken($trip->id, null, null), 
            'has been started', 
            $trip->name
        );

        $this->broadcastTripStatus(
            $trip, 
            ['status' => 'STARTED', 'log_id' => $log_id]
        );

        $trip->update(['status' => true, 'log_id' => $log_id]);

        return $trip;
    }

    public function nearYou($_, array $args)
    {
        try {
            SendPushNotification::dispatch(
                $this->getBusinessTripUsersToken(null, $args['station_id'], null),
                'Qruz captain is so close to you',
                $args['trip_name']
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

        if (!$trip->status) 
            throw new CustomException('This trip has already been ended!');

        $log_id = $trip->log_id;

        $trip->update(['status' => false, 'log_id' => null]);

        $this->updateUserStatus(
            $args['trip_id'],
            ['is_picked_up' => false, 'is_absent' => false]
        );

        $this->closeTripEvent($args, $log_id, $trip);

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
                $args['trip_id'],
                ['is_picked_up' => $is_picked_up],
                $user_ids
            );
            SendPushNotification::dispatch(
                $this->getUsersToken($user_ids),
                $msg,
                $args['trip_name']
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

    protected function updateEventPayload($log_id, $payload)
    {
        try {
            $event = BusinessTripEvent::findOrFail($log_id);
    
            if (array_key_exists('payload', $event->content)) 
                $payload = array_merge($event->content['payload'], $payload);
                
            $event->update(['content' => array_merge($event->content, ['payload' => $payload])]);
        } catch (\Exception $e) {
            //
        }
    }

    protected function closeTripEvent($args, $log_id, $trip)
    {
        try {
            $event = BusinessTripEvent::findOrFail($log_id);
            $locations = BusinessTripEntry::where('log_id', $log_id);
            if ($locations->count()) {
                foreach($locations->get() as $loc) $path[] = $loc->latitude.','.$loc->longitude;
                $updatedData['map_url'] = StaticMapUrl::generatePath(implode('|', $path));
                $locations->delete();
            }
            $ended = ['at' => date("Y-m-d H:i:s")];
            if (array_key_exists('latitude', $args) && array_key_exists('longitude', $args)) {
                $ended['lat'] = $args['latitude'];
                $ended['lng'] = $args['longitude'];
                $this->broadcastTripStatus(
                    $trip, 
                    ['status' => 'ENDED', 'log_id' => $log_id]
                );
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
                    $token = $this->getDriverToken($args['driver_id']);
                    $msg = $args['user_name'] . ' has changed his attendance status to '.$status_text;
                    break;
                default:
                    $token = $this->getUserToken($args['user_id']);
                    $msg = 'The trip captain has changed your attendance status to '.$status_text.', If this isn\'t the case, you could revert it back from inside the trip.';
            }

            SendPushNotification::dispatch($token, $msg, $args['trip_name']);
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

    protected function initTripEvent($args, $log_id)
    {
        try {
            $input = [
                'trip_id' => $args['trip_id'],
                'log_id' => $log_id,
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
            "id" => $trip->id,
            "log_id" => $input['log_id'],
            "name" => $trip->name,
            "status" => $input['status'],
            "partner" => [
                "id" => $trip->partner->id,
                "name" => $trip->partner->name
            ]
        ];
        broadcast(new BusinessTripStatusChanged($data));
    }
}
