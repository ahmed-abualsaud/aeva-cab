<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\Driver;
use App\BusinessTripEvent;
use App\BusinessTrip;
use App\BusinessTripUser;
use App\BusinessTripEntry;
use Illuminate\Support\Str;
use App\Helpers\StaticMapUrl;
use App\BusinessTripAttendance;
use App\Jobs\SendPushNotification;
use App\Exceptions\CustomException;
use App\Events\BusinessTripStatusChanged;

class BusinessTripEventResolver
{

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
                'content' => [ 'started_at' => date("Y-m-d H:i:s") ]
            ];
            BusinessTripEvent::create($input);

            $absent_users = BusinessTripAttendance::absentUsers($args['trip_id']);
            if ($absent_users) 
                $this->updateUserStatus($args['trip_id'], ['is_absent' => true], $absent_users);

            auth('driver')->user()
                ->update(['latitude' => $args['latitude'], 'longitude' => $args['longitude']]);

            $trip->update(['status' => true, 'log_id' => $log_id]);

            SendPushNotification::dispatch(
                $this->getUsersTokens($trip->id, null, null), 
                $trip->name.' has been started.', 
                'Trip Started!'
            );

            $this->broadcastTripStatus(
                $trip, 
                ['status' => 'STARTED', 'log_id' => $log_id]
            );
            
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }

        return $trip;
    }

    public function nearYou($_, array $args)
    {
        try {
            SendPushNotification::dispatch(
                $this->getUsersTokens(null, $args['station_id'], null),
                'Qruz captain is so close to you.',
                'Stand By!'
            );
        } catch (\Exception $e) {
            throw new CustomException("We could not able to notify selected station's users!");
        }

        return "Selected station's users have been notified!";
    }

    public function changeUserStatus($_, array $args)
    {
        try {
            $user = auth('user')->user();
            $input = collect($args)->except(['directive', 'driver_id'])->toArray();
            $status = array();
            $pushMsg = null;
            switch ($args['status']) {
                case 'ARRIVED':
                    $input['status'] = 'ARRIVED';
                    $status['is_arrived'] = true;
                    $pushMsg = $user->name . ' has arrived at the station';
                    break;
                case 'ABSENT':
                    $input['status'] = 'ABSENT';
                    $status['is_absent'] = true;
                    $pushMsg = $user->name . ' is absent today';
                    break;
            }
            $this->updateUserStatus(
                $args['trip_id'],
                $status,
                $user->id
            );
            SendPushNotification::dispatch(
                $this->getDriverToken($args['driver_id']),
                $pushMsg
            );
        } catch (\Exception $e) {
            throw new CustomException('We could not able to notify the captain!');
        }

        return "Qruz captain has been notified.";
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

            $locations = BusinessTripEntry::where('log_id', $trip->log_id);
            if ($locations->count()) {
                foreach($locations->get() as $loc) $path[] = $loc->latitude.','.$loc->longitude;
                $map_url = StaticMapUrl::generatePath(implode('|', $path));
                $updatedData['map_url'] = $map_url;
                $locations->delete();
            }

            $log = BusinessTripEvent::where('log_id', $trip->log_id)->first();
            if ($log) {
                $updatedData['content'] = array_merge($log->content, ['ended_at' => date("Y-m-d H:i:s")]);
                $log->update($updatedData);
            }

            $this->updateUserStatus(
                $args['trip_id'],
                ['is_picked' => false, 'is_arrived' => false, 'is_absent' => false]
            );

            if (array_key_exists('log_id', $args)) { 
                $this->broadcastTripStatus(
                    $trip, 
                    ['status' => 'ENDED', 'log_id' => $trip->log_id]
                );
            }

            $trip->update(['status' => false, 'log_id' => null]);

        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }

        return 'Trip has been ended.';
    }

    protected function updateUserStatus($trip_id, $status, $users = null)
    {
        $usersStatus = BusinessTripUser::where('trip_id', $trip_id);

        if ($users) {
            if (is_array(($users))) {
                $usersStatus->whereIn('user_id', $users);
            } else {
                $usersStatus->where('user_id', $users);
            }
        }

        $usersStatus->update($status);
    }

    public function destroy($_, array $args)
    {
        return BusinessTripEvent::whereIn('log_id', $args['log_id'])->delete();
    }

    protected function pickOrDropUsers($args, $is_picked, $msg, $title)
    {
        try {
            $this->updateUserStatus(
                $args['trip_id'],
                ['is_picked' => $is_picked],
                $args['users']
            );
            SendPushNotification::dispatch(
                $this->getUsersTokens(null, null, $args['users']),
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

    protected function getUsersTokens($trip_id = null, $station_id = null, $users = null)
    {
        if ($users) {
            $tokens = User::select('device_id')
                ->whereIn('id', $users)
                ->pluck('device_id')
                ->toArray();
        } else {
            $tokens = User::select('device_id')
                ->Join('business_trip_users', 'business_trip_users.user_id', '=', 'users.id');
            if ($trip_id) $tokens = $tokens->where('business_trip_users.trip_id', $trip_id);
            if ($station_id) $tokens = $tokens->where('business_trip_users.station_id', $station_id);
            $tokens = $tokens->where('business_trip_users.is_absent', false)
                ->pluck('device_id')
                ->toArray();
        }

        return $tokens;
    }

    protected function getDriverToken($driver_id)
    {
        return Driver::select('device_id')
            ->find($driver_id)->device_id;
    }
}
