<?php

namespace App\GraphQL\Mutations;

use \App\TripLog;
use \App\PartnerTrip;
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

    public function endTrip($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $trip = PartnerTrip::findOrFail($args['trip_id']);
            if (!$trip->status) {
                throw new \Exception('Trip has already ended.');
            }
            $trip->update(['status' => false, 'log_id' => null]);
            $input = Arr::except($args, ['directive']);
            $input['status'] = 'ARRIVED';
            TripLog::create($input);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('We could not find a trip with the provided ID.');
        }

        return 'Trip ended.';
    }

    public function pickUsersUp($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $data = []; 
        $arr = [];

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

}
