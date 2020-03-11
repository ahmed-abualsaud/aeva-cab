<?php

namespace App\GraphQL\Mutations;

use \App\TripLog;
use \App\PartnerTrip;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Arr;

class TripLogResolver
{
    
    public function startTrip($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        
        try {
            $trip = PartnerTrip::findOrFail($args['trip_id']);
            $trip->update(['status' => true]);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Trip status not updated. ' . $e->getMessage());
        }
        $args['status'] = 'STARTED';
        $this->saveLog($rootValue, $args, $context, $resolveInfo);
        return 'Trip started.';
    }

    public function endTrip($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $trip = PartnerTrip::findOrFail($args['trip_id']);
            $trip->update(['status' => false]);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Trip status not updated. ' . $e->getMessage());
        }
        $args['status'] = 'ARRIVED';
        $this->saveLog($rootValue, $args, $context, $resolveInfo);
        return 'Trip ended.';
    }

    public function pickUsersUp($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $data = []; 
        $arr = [];
        foreach($args['users'] as $user) {
            $arr['trip_id'] = $args['trip_id'];
            $arr['latitude'] = $args['latitude'];
            $arr['longitude'] = $args['longitude'];
            $arr['user_id'] = $user;
            $arr['status'] = 'PICKED_UP';
            array_push($data, $arr);
        } 
        try {
            TripLog::insert($data);
        } catch (\Exception $e) {
            throw new \Exception('Users have not been picked up. ' . $e->getMessage());
        }
        return 'Selected users have been picked up.';
    }

    public function updateDriverLocation($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $this->saveLog($rootValue, $args, $context, $resolveInfo);
        return 'Driver location has been updated successfully.';
    }

    public function changeTripUserStatus($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $this->saveLog($rootValue, $args, $context, $resolveInfo);
        return 'Your status has been changed into ' . $args['status'];
    }

    protected function saveLog($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    { 
        try {
            $input = Arr::except($args, ['directive']);
            TripLog::create($input);
        } catch (\Exception $e) {
            throw new \Exception('Trip log not created. ' . $e->getMessage());
        }
    }
}
