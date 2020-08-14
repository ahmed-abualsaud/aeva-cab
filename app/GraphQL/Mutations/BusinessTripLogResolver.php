<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\Driver;
use App\TripLog;
use Carbon\Carbon;
use App\DeviceToken;
use App\BusinessTrip;
use App\DriverVehicle;
use App\BusinessTripUser;
use App\Mail\DefaultMail;
use Illuminate\Support\Arr;
use App\Events\TripLogPosted;
use App\Jobs\SendPushNotification;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Mail;
use App\Events\DriverLocationUpdated;
use GraphQL\Type\Definition\ResolveInfo;
use App\Events\BusinessTripStatusChanged;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BusinessTripLogResolver
{
    
    public function startTrip($_, array $args)
    {
        try {
            $trip = BusinessTrip::findOrFail($args['trip_id']);
            if ($trip->status) throw new CustomException('Trip has already been started.');
            $log_id = uniqid() . 'T' . $args['trip_id'];

            DriverVehicle::where('driver_id', $trip->driver_id)
                ->where('vehicle_id', $trip->vehicle_id)
                ->update([
                    'status' => 'RIDING', 
                    'trip_type' => 'App\BusinessTrip', 
                    'trip_id' => $trip->id
                ]);

            $trip->update(['status' => true, 'log_id' => $log_id]);
            $input = Arr::except($args, ['directive']);
            $input['status'] = 'STARTED';
            $input['log_id'] = $log_id;
            TripLog::create($input);
        } catch (ModelNotFoundException $e) {
            throw new CustomException('We could not find a trip with the provided ID.');
        }

        $push_msg = $trip->name . ' has been started.';
        SendPushNotification::dispatch($this->getTokens($trip->id), $push_msg);
        
        $this->broadcastTripLog($input);

        $this->broadcastTripStatus($trip, $input);
 
        return $trip;
    }

    public function nearYou($_, array $args)
    {
        $tokens = BusinessTripUser::where('station_id', $args['station_id'])
            ->where('device_tokens.tokenable_type', 'App\User')
            ->join('device_tokens', 'device_tokens.tokenable_id', '=', 'business_trip_users.user_id')
            ->select('device_tokens.device_id')
            ->pluck('device_id')
            ->toArray();
        
        $push_msg = 'Our driver is so close to you, please stand by.';
        SendPushNotification::dispatch($tokens, $push_msg);

        return "Notification has been sent to selected station users.";
    }

    public function userArrived($_, array $args)
    {  
        try {
            $user = User::select('name')->findOrFail($args['user_id']);
            $token = DeviceToken::where('tokenable_id', $args['driver_id'])
                ->where('tokenable_type', 'App\Driver')
                ->select('device_id')
                ->pluck('device_id')
                ->toArray();

            $input = collect($args)->except(['directive', 'driver_id'])->toArray();
            $input['status'] = 'ARRIVED';
            TripLog::create($input);
        } catch (\Exception $e) {
            throw new CustomException('Notification has not been sent to the driver. ' . $e->getMessage());
        }
        
        $push_msg = $user->name . ' has arrived';
        SendPushNotification::dispatch($token, $push_msg);

        $this->broadcastTripLog($input, $user->name);

        return "Notification has been sent to the driver";
    }

    public function pickUsersUp($_, array $args)
    {
        $data = array(); 
        $arr = array();

        foreach($args['users'] as $user) {
            $arr['log_id'] = $args['log_id'];
            $arr['trip_id'] = $args['trip_id'];
            $arr['latitude'] = $args['latitude'];
            $arr['longitude'] = $args['longitude'];
            $arr['user_id'] = $user['id'];
            $arr['status'] = $user['is_picked_up'] ? 'PICKED_UP' : 'NOT_PICKED_UP';
            $arr['created_at'] = $arr['updated_at'] = now();
            array_push($data, $arr);
        } 
        
        $filterNewPickedUp = Arr::where($args['users'], function ($value, $key) {
            return $value['is_picked_up'];
        });
        $pickedUp = collect($filterNewPickedUp)->pluck('id')->toArray();
        
        $user_id = collect($args['users'])->pluck('id');
        $tripLogs = TripLog::where('log_id', $args['log_id'])
            ->where('status', 'PICKED_UP')
            ->orWhere('status', 'NOT_PICKED_UP')
            ->whereIn('user_id', $user_id);

        $oldPickedUp = $tripLogs->get()->where('status', 'PICKED_UP')->pluck('user_id')->toArray();
        
        $newPickedUp = array_diff($pickedUp, $oldPickedUp);

        if ($newPickedUp) {
            $devices = DeviceToken::where('tokenable_type', 'App\User')
                ->whereIn('tokenable_id', $newPickedUp)
                ->select('device_id')
                ->pluck('device_id')
                ->toArray();

            $push_msg = 'Have a wonderful trip. May you be happy and safe throughout this trip.';

            $usernames = User::select('name')
                ->whereIn('id', $newPickedUp)
                ->pluck('name')
                ->toArray();

            $input = [
                "trip_id" => $args['trip_id'],
                "log_id" => $args['log_id'],
                "status" => "PICKED_UP",
                "latitude" => $args['latitude'],
                "longitude" => $args['longitude']
            ];

            SendPushNotification::dispatch($devices, $push_msg);

            $this->broadcastTripLog($input, implode(', ', $usernames));

        }

        $tripLogs->delete();
        TripLog::insert($data);

        return 'Selected users status have been changed.';
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

            DriverVehicle::where('driver_id', $trip->driver_id)
                ->where('vehicle_id', $trip->vehicle_id)
                ->update(['status' => 'ACTIVE', 'trip_type' => null, 'trip_id' => null]);

            $trip->update(['status' => false, 'log_id' => null]);
            $input = Arr::except($args, ['directive']);
            $input['status'] = 'ENDED';
            TripLog::create($input);
        } catch (ModelNotFoundException $e) {
            throw new CustomException('We could not find a trip with the provided ID.');
        }

        $push_msg = $trip->name . ' has arrived. Have a great time.';
        SendPushNotification::dispatch($this->getTokens($trip->id), $push_msg);

        $this->broadcastTripLog($input);

        $this->broadcastTripStatus($trip, $input);

        return 'Trip has been ended.';
    }

    protected function getTokens($tripID)
    {
        $tokens = DeviceToken::Join('business_trip_users', function ($join) {
            $join->on('business_trip_users.user_id', '=', 'device_tokens.tokenable_id')
                ->where('device_tokens.tokenable_type', '=', 'App\User');
            })
            ->where('business_trip_users.trip_id', $tripID)
            ->select('device_tokens.device_id')
            ->pluck('device_tokens.device_id')
            ->toArray();

        return $tokens;
    }

    protected function broadcastTripLog($input, $user = null)
    {
        $log = [
            "created_at" => date("Y-m-d H:i:s"),
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
        
        $msg = $trip->name . ' trip has been ' . strtolower($input['status']);
        Mail::to(config('custom.mail_to_address'))
            ->cc($trip->partner->email)
            ->send(new DefaultMail($msg, $msg));
    }

}
