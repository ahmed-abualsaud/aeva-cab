<?php

namespace App\GraphQL\Mutations;

use App\BusinessTrip;
use App\BusinessTripEntry;
use App\BusinessTripEvent;
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
        try {
            $trip = BusinessTrip::findOrFail($args['trip_id']);
            if ($trip->status) 
                throw new \Exception('This Trip has already been started!');
            $log_id = $trip->subscription_code.'-'.Str::random(4).'-'.uniqid();

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

            $absent_users = BusinessTripAttendance::whereAbsent($args['trip_id']);
            if ($absent_users) 
                $this->updateUserStatus($args['trip_id'], ['is_absent' => true], $absent_users);

            auth('driver')->user()
                ->update(['latitude' => $args['latitude'], 'longitude' => $args['longitude']]);

            SendPushNotification::dispatch(
                $this->getUsersTokens($trip->id, null, null, null), 
                $trip->name.' has been started.', 
                'Trip Started!'
            );

            $this->broadcastTripStatus(
                $trip, 
                ['status' => 'STARTED', 'log_id' => $log_id]
            );

            $trip->update(['status' => true, 'log_id' => $log_id]);
            
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }

        return $trip;
    }

    public function nearYou($_, array $args)
    {
        try {
            SendPushNotification::dispatch(
                $this->getUsersTokens(null, $args['station_id'], null, null),
                'Qruz captain is so close to you.',
                'Stand By!'
            );
        } catch (\Exception $e) {
            throw new CustomException("We could not able to notify selected station's users!");
        }

        return "Selected station's users have been notified!";
    }

    public function changeUserStatus()
    {
        return "Qruz captain has been notified.";
    }

    public function changeBusinessTripPickupStatus($_, array $args)
    {
        $this->updateUserStatus(
            $args['trip_id'], ['is_picked_up' => $args['is_picked_up']], $args['user_id']
        );

        return "Pick up status has been changed successfully";
    }

    public function pickUsers($_, array $args)
    {
        $msg = 'May you be happy and safe throughout this trip.';
        $this->pickOrDropUsers($args, true, $msg, 'Welcome!');

        return $msg;
    }

    public function dropUsers($_, array $args)
    {
        $msg = 'We can\'t wait to see you next time.';
        $this->pickOrDropUsers($args, false, $msg, 'Bye!');

        return $msg;
    }

    public function updateDriverLocation($_, array $args)
    {
        try {
            $location = [
                'latitude' => $args['latitude'],
                'longitude' => $args['longitude']
            ];
            auth('driver')->user()->update($location);
            $location['log_id'] = $args['log_id'];
            BusinessTripEntry::create($location);
        } catch (\Exception $e) {
            throw new CustomException('We could not able to update your location!');
        }

        return "Your location has been updated.";
    }

    public function endTrip($_, array $args)
    {
        try {
            $trip = BusinessTrip::findOrFail($args['trip_id']);
            if (!$trip->status) 
                throw new \Exception('This trip has already been ended!');

            $event = BusinessTripEvent::find($trip->log_id);

            if ($event) {
                
                $path[] = $event->content['started']['lat'].','.$event->content['started']['lng'];

                $locations = BusinessTripEntry::where('log_id', $trip->log_id);
                if ($locations->count()) {
                    foreach($locations->get() as $loc) $path[] = $loc->latitude.','.$loc->longitude;
                    $locations->delete();
                }
            
                $ended = ['at' => date("Y-m-d H:i:s")];

                if (array_key_exists('latitude', $args)) {
                    $path[] = $args['latitude'].','.$args['longitude'];
                    $ended['lat'] = $args['latitude'];
                    $ended['lng'] = $args['longitude'];
                    $this->broadcastTripStatus(
                        $trip, 
                        ['status' => 'ENDED', 'log_id' => $trip->log_id]
                    );
                }

                $map_url = StaticMapUrl::generatePath(implode('|', $path));
                $updatedData = [
                    'map_url' => $map_url,
                    'content' => array_merge($event->content, ['ended' => $ended])
                ];
                $event->update($updatedData);
            }

            $this->updateUserStatus(
                $args['trip_id'],
                ['is_picked_up' => false, 'is_absent' => false]
            );

            $trip->update(['status' => false, 'log_id' => null]);

        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }

        return 'Trip has been ended.';
    }

    public function destroy($_, array $args)
    {
        return BusinessTripEvent::whereIn('log_id', $args['log_id'])->delete();
    }

    protected function pickOrDropUsers($args, $is_picked_up, $msg, $title)
    {
        try {
            $this->updateUserStatus(
                $args['trip_id'],
                ['is_picked_up' => $is_picked_up],
                $args['users']
            );
            SendPushNotification::dispatch(
                $this->getUsersTokens(null, null, $args['users'], null),
                $msg,
                $title
            );
        } catch (\Exception $e) {
            throw new CustomException('We could not able to change selected users status!');
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
                "name" => $trip->partner->name,
                "logo" => $trip->partner->logo,
            ]
        ];
        broadcast(new BusinessTripStatusChanged($data));
    }
}
