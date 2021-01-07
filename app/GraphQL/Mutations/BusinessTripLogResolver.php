<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\Driver;
use App\TripLog;
use App\BusinessTrip;
use App\BusinessTripUser;
use Illuminate\Support\Arr;
use App\BusinessTripAttendance;
use App\Jobs\SendPushNotification;
use App\Exceptions\CustomException;
use App\Events\BusinessTripStatusChanged;

class BusinessTripLogResolver
{

    public function startTrip($_, array $args)
    {
        try {
            $trip = BusinessTrip::findOrFail($args['trip_id']);
            if ($trip->status) throw new \Exception('This Trip has already been started!');
            $log_id = $trip->subscription_code . '@' . uniqid();
            $trip->update(['status' => true, 'log_id' => $log_id]);
            $input = Arr::except($args, ['directive']);
            $input['status'] = 'STARTED'; $input['log_id'] = $log_id;
            TripLog::create($input);

            $absent_users = BusinessTripAttendance::absentUsers($args['trip_id']);
            if ($absent_users) 
                $this->updateUserStatus($args['trip_id'], ['is_absent' => true], $absent_users);

            SendPushNotification::dispatch(
                $this->getUsersTokens($trip->id, null, null), 
                $trip->name.' has been started.', 
                'Trip Started!'
            );

            auth('driver')->user()
                ->update(['latitude' => $args['latitude'], 'longitude' => $args['longitude']]);

            $this->broadcastTripStatus($trip, $input);
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
            $input = collect($args)->except(['directive'])->toArray();
            $location = [
                'latitude' => $input['latitude'],
                'longitude' => $input['longitude']
            ];
            TripLog::create($input);
            auth('driver')->user()->update($location);
        } catch (\Exception $e) {
            throw new CustomException('We could not able to update your location!');
        }

        return "Your location has been updated.";
    }

    public function endTrip($_, array $args)
    {
        try {
            $trip = BusinessTrip::findOrFail($args['trip_id']);
            if (!$trip->status) throw new \Exception('Trip has already been ended!');
            $trip->update(['status' => false, 'log_id' => null]);
            $input = Arr::except($args, ['directive']);
            $input['status'] = 'ENDED';
            TripLog::create($input);

            SendPushNotification::dispatch(
                $this->getUsersTokens($trip->id, null, null),
                $trip->name . ' has been ended. Thanks for choosing Qruz.',
                'Trip Ended!'
            );
            $this->updateUserStatus(
                $args['trip_id'],
                ['is_picked' => false, 'is_arrived' => false, 'is_absent' => false]
            );
            $this->broadcastTripStatus($trip, $input);
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }

        return 'Trip has been ended.';
    }

    public function deleteBusinessTripLog($_, array $args)
    {
        return TripLog::whereIn('log_id', $args['log_id'])->delete();
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
