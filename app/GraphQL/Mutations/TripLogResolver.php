<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\Driver;
use App\TripLog;
use App\PartnerTrip;
use App\DeviceToken;
use App\PartnerTripUser;
use App\Jobs\PushNotification;
use Carbon\Carbon;
use Illuminate\Support\Arr;
// use App\Events\DriverLocationUpdated; 
// use App\Events\TripLogPost; 
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TripLogResolver
{
    
    public function startTrip($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $trip = PartnerTrip::findOrFail($args['trip_id']);
            if ($trip->status) {
                throw new \Exception('Trip has already started.');
            }

            $logID = uniqid() . 'T' . $args['trip_id'];

            $notificationMsg = $trip->name . ' has started.';
            $data = [
                "status" => "TRIP_STARTED",
                "logID" => $logID
            ];
            PushNotification::dispatch($this->getTokens($trip), $notificationMsg, $data);

            $trip->update(['status' => true, 'log_id' => $logID]);
            $input = Arr::except($args, ['directive']);
            $input['status'] = 'STARTED';
            $input['log_id'] = $logID;
            TripLog::create($input);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('We could not find a trip with the provided ID.');
        }

        return $trip;
    }

    public function nearYou($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $tokens = PartnerTripUser::where('station_id', $args['station_id'])
            ->where('device_tokens.tokenable_type', 'App\User')
            ->join('device_tokens', 'device_tokens.tokenable_id', '=', 'partner_trip_users.user_id')
            ->select('device_tokens.device_id')
            ->pluck('device_id');
        
        $notificationMsg = 'Our driver is so close to you, kindly stand by.';
        $data = ["status" => "NEAR_YOU"];
        PushNotification::dispatch($tokens, $notificationMsg, $data);

        return "Notification has been sent to selected station users.";
    }

    public function userArrived($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {  
        try {
            $user = auth('user')->user();

            $token = DeviceToken::where('tokenable_id', $args['driver_id'])
                ->where('tokenable_type', 'App\Driver')
                ->select('device_id')
                ->pluck('device_id');
            
            $notificationMsg = $user->name . ' has arrived';
            $data = ["status" => "USER_ARRIVED"];
            PushNotification::dispatch($token, $notificationMsg, $data);

            $input = collect($args)->except(['directive', 'driver_id'])->toArray();
            $input['status'] = 'ARRIVED';
            $input['user_id'] = $user->id;
            TripLog::create($input);
        } catch (\Exception $e) {
            throw new \Exception('Notification has not been sent to the driver. ' . $e->getMessage());
        }

        return "Notification has been sent to the driver";
    }

    public function endTrip($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $trip = PartnerTrip::findOrFail($args['trip_id']);
            if (!$trip->status) {
                throw new \Exception('Trip has already ended.');
            }

            $comeBack = '';
            if ($trip->return_time) {
                $comeBack = ' We will return back to you at ' . Carbon::parse($trip->return_time)->format('g:i A');
            }

            $notificationMsg = 'We have arrived. Have a great time.' . $comeBack;
            $data = ["status" => "TRIP_ENDED"];
            PushNotification::dispatch($this->getTokens($trip), $notificationMsg, $data);

            $trip->update(['status' => false, 'log_id' => null]);
            $input = Arr::except($args, ['directive']);
            $input['status'] = 'ENDED';
            TripLog::create($input);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('We could not find a trip with the provided ID.');
        }

        // $log = [
        //     "created_at" => Carbon::now(),
        //     "status" => $input['status'],
        //     "latitude" => $args['latitude'],
        //     "longitude" => $args['longitude'],
        //     "user" => null
        // ];

        // broadcast(new TripLogPost($log, 'business.'.$args['trip_id']))->toOthers();

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
            ->pluck('device_id');

            $notificationMsg = 'Have a wonderful trip. May you be happy and safe throughout this trip.';
            $pushData = ["status" => "PICKED_UP"];
            PushNotification::dispatch($devices, $notificationMsg, $pushData);
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

        try {
            Driver::findOrFail($args['driver_id'])->update($location);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Driver location has not updated. ' . $e->getMessage());
        }

        // if (array_key_exists('trip_id', $args) && $args['trip_id']) {
            // broadcast(new DriverLocationUpdated($location, 'business.'.$args['trip_id']))->toOthers();
        // }

        return 'Driver location has been updated successfully.';
    }

    public function changeTripUserStatus($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $tripLog = TripLog::where('log_id', $args['log_id'])
                ->where('user_id', $args['user_id'])->firstOrFail();
            $tripLog->update(['status' => $args['status'], 'updated_at' => now()]);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('We could not find a trip log with the provided log ID.' . $e->getMessage());
        }
        
        return 'Your status has been changed into ' . $args['status'];
    }

    protected function getTokens($trip)
    {
        $tokens = array();
        foreach ($trip->users as $user) {
            $deviceIDs = $user->deviceTokens->pluck('device_id');
            array_push($tokens, $deviceIDs);
        }
        $tokens = Arr::collapse($tokens);

        return array_filter($tokens);
    }

}
