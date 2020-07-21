<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\Driver;
use App\TripLog;
use Carbon\Carbon;
use App\DeviceToken;
use App\PartnerTrip;
use App\DriverVehicle;
use App\PartnerTripUser;
use App\Events\TripLogPost; 
use Illuminate\Support\Arr;
use App\Jobs\PushNotification;
use App\Exceptions\CustomException;
use App\Events\DriverLocationUpdated;
use GraphQL\Type\Definition\ResolveInfo;
use App\Notifications\BusinessTripStatus;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TripLogResolver
{
    
    public function startTrip($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $trip = PartnerTrip::findOrFail($args['trip_id']);
            if ($trip->status) throw new CustomException('Trip has already started.');
            $logID = uniqid() . 'T' . $args['trip_id'];

            $notificationMsg = $trip->name . ' has started.';
            $data = [
                "status" => "TRIP_STARTED",
                "logID" => $logID
            ];
            PushNotification::dispatch($this->getTokens($trip->id), $notificationMsg, $data);

            DriverVehicle::where('driver_id', $trip->driver_id)
                ->where('vehicle_id', $trip->vehicle_id)
                ->update(['status' => 'RIDING', 'trip_type' => 'BUSINESS', 'trip_id' => $trip->id]);

            $trip->update(['status' => true, 'log_id' => $logID]);
            $input = Arr::except($args, ['directive']);
            $input['status'] = 'STARTED';
            $input['log_id'] = $logID;
            TripLog::create($input);
        } catch (ModelNotFoundException $e) {
            throw new CustomException('We could not find a trip with the provided ID.');
        }

        $this->broadcastTripLog($input);

        $dbNotification = [
            "id" => $trip->id,
            "name" => $trip->name,
            "status" => $input['status']
        ];
        $trip->partner->notify(new BusinessTripStatus($dbNotification));

        return $trip;
    }

    public function nearYou($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $tokens = PartnerTripUser::where('station_id', $args['station_id'])
            ->where('device_tokens.tokenable_type', 'App\User')
            ->join('device_tokens', 'device_tokens.tokenable_id', '=', 'partner_trip_users.user_id')
            ->select('device_tokens.device_id')
            ->pluck('device_id')
            ->toArray();
        
        $notificationMsg = 'Our driver is so close to you, kindly stand by.';
        $data = ["status" => "NEAR_YOU"];
        PushNotification::dispatch($tokens, $notificationMsg, $data);

        return "Notification has been sent to selected station users.";
    }

    public function userArrived($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
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
        
        $notificationMsg = $user->name . ' has arrived';
        $data = ["status" => "ARRIVED"];
        PushNotification::dispatch($token, $notificationMsg, $data);

        $this->broadcastTripLog($input, $user->name);

        return "Notification has been sent to the driver";
    }

    public function endTrip($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $trip = PartnerTrip::findOrFail($args['trip_id']);
            if (!$trip->status) throw new CustomException('Trip has already ended.');

            $notificationMsg = $trip->name . ' has arrived. Have a great time.';

            if ($trip->return_time) {
                $notificationMsg .= ' Return trip will be at ' . Carbon::parse($trip->return_time)->format('g:i A');
            }

            $data = ["status" => "TRIP_ENDED"];
            PushNotification::dispatch($this->getTokens($trip->id), $notificationMsg, $data);

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

        $this->broadcastTripLog($input);

        $dbNotification = [
            "id" => $trip->id,
            "name" => $trip->name,
            "status" => $input['status']
        ];
        $trip->partner->notify(new BusinessTripStatus($dbNotification));

        return 'Trip has ended.';
    }

    public function pickUsersUp($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
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

            $notificationMsg = 'Have a wonderful trip. May you be happy and safe throughout this trip.';
            $pushData = ["status" => "PICKED_UP"];
            PushNotification::dispatch($devices, $notificationMsg, $pushData);

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

            $this->broadcastTripLog($input, implode(', ', $usernames));

        }

        $tripLogs->delete();
        TripLog::insert($data);

        return 'Selected users status have been changed.';
    }

    public function updateDriverLocation($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $location = [
            'latitude' => $args['latitude'],
            'longitude' => $args['longitude']
        ];

        if (array_key_exists('trip_id', $args) && $args['trip_id']) {
            return broadcast(new DriverLocationUpdated($location, 'business.'.$args['trip_id']))
                ->toOthers();
        } else if (array_key_exists('driver_id', $args) && $args['driver_id']) {
            return Driver::findOrFail($args['driver_id'])->update($location);
        } else {
            return auth('driver')->user()->update($location);
        }

    }

    public function changeTripUserStatus($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
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

    protected function getTokens($tripID)
    {
        $tokens = DeviceToken::Join('partner_trip_users', function ($join) {
            $join->on('partner_trip_users.user_id', '=', 'device_tokens.tokenable_id')
                ->where('device_tokens.tokenable_type', '=', 'App\User');
            })
            ->where('partner_trip_users.trip_id', $tripID)
            ->select('device_tokens.device_id')
            ->pluck('device_tokens.device_id')
            ->toArray();

        return $tokens;
    }

    protected function broadcastTripLog($input, $user = null)
    {
        $log = [
            "id" => $input['log_id'],
            "post" => [
                "created_at" => date("Y-m-d H:i:s"),
                "status" => $input['status'],
                "latitude" => $input['latitude'],
                "longitude" => $input['longitude'],
                "user" => $user
            ]
        ];

        broadcast(new TripLogPost($log, 'business.'.$input['trip_id']))->toOthers();
    }

}
