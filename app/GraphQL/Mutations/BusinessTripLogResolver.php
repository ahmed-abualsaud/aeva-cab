<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\Driver;
use App\TripLog;
use App\BusinessTrip;
// use App\DriverVehicle;
use App\BusinessTripUser;
// use App\Mail\DefaultMail;
use Illuminate\Support\Arr;
use App\Events\TripLogPosted;
use App\Jobs\SendPushNotification;
use App\Exceptions\CustomException;
// use Illuminate\Support\Facades\Mail;
use App\Events\DriverLocationUpdated;
use App\Events\BusinessTripStatusChanged;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BusinessTripLogResolver
{
    
    public function startTrip($_, array $args)
    {
        try {
            $trip = BusinessTrip::findOrFail($args['trip_id']);

            if ($trip->status) throw new CustomException('Trip has already been started.');

            $log_id = $trip->subscription_code .'@'. uniqid();

            $trip->update(['status' => true, 'log_id' => $log_id]);

            $input = Arr::except($args, ['directive']);
            $input['status'] = 'STARTED';
            $input['log_id'] = $log_id;

            TripLog::create($input);
        } catch (ModelNotFoundException $e) {
            throw new CustomException('We could not find a trip with the provided ID.');
        }

        // $this->changeDriverStatus($trip, 'RIDING');
            
        $this->changeUserStatus($args['trip_id'], ['is_picked' => false, 'is_arrived' => false]);

        $tokens = $this->getUsersTokens($trip->id, null, null);
        $push_msg = $trip->name . ' has been started.';
        SendPushNotification::dispatch($tokens, $push_msg, 'Trip Started!');
        
        $this->broadcastTripLog($input);

        $this->broadcastTripStatus($trip, $input);
 
        return $trip;
    }

    public function nearYou($_, array $args)
    {
        $tokens = $this->getUsersTokens(null, $args['station_id'], null);
        $push_msg = 'Our driver is so close to you, please stand by.';
        SendPushNotification::dispatch($tokens, $push_msg, 'Stand By!');

        return "Notification has been sent to selected station users.";
    }

    public function userArrived($_, array $args)
    {  
        try {
            $user = User::select('name')->findOrFail($args['user_id']);
            $input = collect($args)->except(['directive', 'driver_id'])->toArray();
            $input['status'] = 'ARRIVED';
            TripLog::create($input);
        } catch (\Exception $e) {
            throw new CustomException('Notification has not been sent to the driver. ' . $e->getMessage());
        }

        BusinessTripUser::where('trip_id', $args['trip_id'])
            ->where('user_id', $args['user_id'])
            ->update(['is_arrived' => true]);

        $token = $this->getDriverToken($args['driver_id']);
        $push_msg = $user->name . ' has arrived';
        SendPushNotification::dispatch($token, $push_msg);

        $this->broadcastTripLog($input, $user->name);

        return "Notification has been sent to the driver";
    }

    public function pickUsers($_, array $args)
    {
        $msg = 'May you be happy and safe throughout this trip.';
        $this->pickOrDropUsers($args, 'PICKED_UP', true, $msg, 'Welcome!');

        return $msg;
    }

    public function dropUsers($_, array $args)
    {
        $msg = 'We can\'t wait to see you next time.';
        $this->pickOrDropUsers($args, 'DROPPED_OFF', false, $msg, 'Bye!');

        return $msg;
    }

    public function updateDriverLocation($_, array $args)
    {
        $location = [
            'latitude' => $args['latitude'],
            'longitude' => $args['longitude']
        ];

        if (array_key_exists('trip_id', $args) && $args['trip_id']) {
            $channel = 'App.BusinessTrip.'.$args['trip_id'];
            return broadcast(new DriverLocationUpdated($channel, $location));
        } else if (array_key_exists('driver_id', $args) && $args['driver_id']) {
            return Driver::findOrFail($args['driver_id'])->update($location);
        } else {
            return auth('driver')->user()->update($location);
        }

    }

    public function changeTripUserStatus($_, array $args)
    {
        try {
            $tripLog = TripLog::where('log_id', $args['log_id'])
                ->where('user_id', $args['user_id'])
                ->firstOrFail();
            $tripLog->update(['status' => $args['status'], 'updated_at' => now()]);
        } catch (ModelNotFoundException $e) {
            throw new CustomException('We could not find a trip log with the provided log ID.' . $e->getMessage());
        }

        $user = User::select('name')->find($args['user_id']);

        $input = [
            "trip_id" => $args['trip_id'],
            "log_id" => $args['log_id'],
            "status" => $args['status'],
            "latitude" => $args['latitude'],
            "longitude" => $args['longitude']
        ];

        $this->broadcastTripLog($input, $user->name);
        
        return 'Your status has been changed into ' . $args['status'];
    }

    public function endTrip($_, array $args)
    {
        try {
            $trip = BusinessTrip::findOrFail($args['trip_id']);
            if (!$trip->status) throw new CustomException('Trip has already been ended.');

            $trip->update(['status' => false, 'log_id' => null]);

            $input = Arr::except($args, ['directive']);
            $input['status'] = 'ENDED';

            TripLog::create($input);
        } catch (ModelNotFoundException $e) {
            throw new CustomException('We could not find a trip with the provided ID.');
        }

        // $this->changeDriverStatus($trip, 'ACTIVE');
        
        $this->changeUserStatus($args['trip_id'], ['is_picked' => false, 'is_arrived' => false]);

        $push_msg = $trip->name . ' has been ended. Thanks for choosing Qruz.';
        $tokens = $this->getUsersTokens($trip->id, null, null);
        SendPushNotification::dispatch($tokens, $push_msg, 'Trip Ended!');

        $this->broadcastTripLog($input);

        $this->broadcastTripStatus($trip, $input);

        return 'Trip has been ended.';
    }

    public function deleteBusinessTripLog($_, array $args)
    {
        return TripLog::whereIn('log_id', $args['log_id'])->delete();
    }

    protected function getUsersTokens($trip_id = null, $station_id = null, $users = null)
    {
        if ($users) {
            $tokens = User::whereIn('id', $users)
                ->select('device_id')
                ->pluck('device_id')
                ->toArray();

            // $tokens = DeviceToken::where('tokenable_type', 'App\User')
            //     ->whereIn('tokenable_id', $users)
            //     ->select('device_id')
            //     ->pluck('device_id')
            //     ->toArray();
        } else {
            $tokens = User::Join('business_trip_users', 'business_trip_users.user_id', '=', 'users.id');

            // $tokens = DeviceToken::Join('business_trip_users', function ($join) {
            //     $join->on('business_trip_users.user_id', '=', 'device_tokens.tokenable_id')
            //         ->where('device_tokens.tokenable_type', '=', 'App\User');
            //     });
    
            if ($trip_id) $tokens = $tokens->where('business_trip_users.trip_id', $trip_id);

            if ($station_id) $tokens = $tokens->where('business_trip_users.station_id', $station_id);

            $tokens = $tokens->select('device_id')
                ->pluck('device_id')
                ->toArray();
        }

        return $tokens;
    }

    protected function getDriverToken($driver_id)
    {
        $token = Driver::where('id', $driver_id)
            ->select('device_id')
            ->pluck('device_id')
            ->toArray();

        // $token = DeviceToken::where('tokenable_id', $driver_id)
        //     ->where('tokenable_type', 'App\Driver')
        //     ->select('device_id')
        //     ->pluck('device_id')
        //     ->toArray();

        return $token;
    }

    protected function broadcastTripLog($input, $user = null)
    {
        $log = [
            "time" => date("h:i a"),
            "status" => $input['status'],
            "latitude" => $input['latitude'],
            "longitude" => $input['longitude'],
            "user" => $user,
            "__typename" => "BusinessTripLogResponse"
        ];

        $channel = 'App.BusinessTrip.'.$input['log_id'];

        broadcast(new TripLogPosted($channel, $log));
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
        
        // $msg = $trip->name . ' trip has been ' . strtolower($input['status']);
        // Mail::to(config('custom.mail_to_address'))
        //     ->cc($trip->partner->email)
        //     ->send(new DefaultMail($msg, $msg));
    }

    protected function changeUserStatus($trip_id, $status, $users = null)
    {
        $usersStatus = BusinessTripUser::where('trip_id', $trip_id);

        if ($users) $usersStatus->whereIn('user_id', $users);
        
        $usersStatus->update($status);
    }

    // protected function changeDriverStatus($trip, $status)
    // {
    //     DriverVehicle::where('driver_id', $trip->driver_id)
    //         ->where('vehicle_id', $trip->vehicle_id)
    //         ->update([
    //             'status' => $status, 
    //             'trip_type' => $status === 'RIDING' ? 'App\BusinessTrip' : null, 
    //             'trip_id' => $status === 'RIDING' ? $trip->id : null
    //         ]);
    // }

    protected function pickOrDropUsers($args, $status, $is_picked, $msg, $title)
    {
        $data = []; $arr = [];
        foreach($args['users'] as $user) {
            $arr['log_id'] = $args['log_id'];
            $arr['trip_id'] = $args['trip_id'];
            $arr['latitude'] = $args['latitude'];
            $arr['longitude'] = $args['longitude'];
            $arr['user_id'] = $user;
            $arr['status'] = $status;
            $arr['created_at'] = $arr['updated_at'] = now();
            array_push($data, $arr);
        } 

        $this->changeUserStatus($args['trip_id'], ['is_picked' => $is_picked], $args['users']);

        $tokens = $this->getUsersTokens(null, null, $args['users']);
        SendPushNotification::dispatch($tokens, $msg, $title);
        
        $usernames = User::select('name')
            ->whereIn('id', $args['users'])
            ->pluck('name')
            ->toArray();

        $input = [
            "trip_id" => $args['trip_id'],
            "log_id" => $args['log_id'],
            "status" => $status,
            "latitude" => $args['latitude'],
            "longitude" => $args['longitude']
        ];

        $this->broadcastTripLog($input, implode(', ', $usernames));

        TripLog::insert($data);
    }

}
