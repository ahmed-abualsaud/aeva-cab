<?php

namespace App\GraphQL\Mutations;

use App\TripLog;
use App\PartnerTrip;
use App\PartnerTripUser;
use App\Jobs\PushNotification;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Arr;
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
        if (!array_key_exists('station_id', $args)) {
            throw new \Exception('Station ID is required, but not provided.');
        }

        $tokens = PartnerTripUser::where('station_id', $args['station_id'])
            ->where('device_tokens.tokenable_type', 'App\User')
            ->join('device_tokens', 'device_tokens.tokenable_id', '=', 'partner_trip_users.user_id')
            ->select('device_tokens.device_id')
            ->pluck('device_id');
        
        $notificationMsg = 'Our driver is so close to you, kindly stand by.';
        $data = ["status" => "NEAR_YOU"];
        PushNotification::dispatch($tokens, $notificationMsg, $data);

        $input = collect($args)->except(['directive', 'station_id'])->toArray();
        $input['status'] = 'NEAR_YOU';
        TripLog::create($input);

        return "Notification has been sent to selected station users.";
    }

    public function endTrip($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $trip = PartnerTrip::findOrFail($args['trip_id']);
            if (!$trip->status) {
                throw new \Exception('Trip has already ended.');
            }

            $notificationMsg = 'We have arrived';
            $data = ["status" => "TRIP_ENDED"];
            PushNotification::dispatch($this->getTokens($trip), $notificationMsg, $data);

            $trip->update(['status' => false, 'log_id' => null]);
            $input = Arr::except($args, ['directive']);
            $input['status'] = 'ARRIVED';
            TripLog::create($input);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('We could not find a trip with the provided ID.');
        }

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

        $user_id = collect($args['users'])->pluck('id');
        TripLog::where('log_id', $args['log_id'])
        ->whereIn('user_id', $user_id)
        ->delete();

        try {
            TripLog::insert($data);
        } catch (\Exception $e) {
            throw new \Exception('Trip ID or User ID is invalid. ' . $e->getMessage());
        }

        return 'Selected users status have been changed.';
    }

    public function updateDriverLocation($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {    
        try {
            $input = Arr::except($args, ['directive']);
            TripLog::create($input);
        } catch (\Exception $e) {
            throw new \Exception('Driver location has not updated. ' . $e->getMessage());
        }

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
