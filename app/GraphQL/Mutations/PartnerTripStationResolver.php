<?php

namespace App\GraphQL\Mutations;

use App\PartnerTripUser;
use App\PartnerTripStation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class PartnerTripStationResolver
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function create($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $data = array(); 
            $arr = array();
            foreach($args['stations'] as $station) {
                $arr['trip_id'] = $args['trip_id'];
                $arr['name'] = $station['name'];
                $arr['latitude'] = $station['latitude'];
                $arr['longitude'] = $station['longitude'];
                $arr['state'] = $station['state'];
                $arr['accepted_at'] = $station['accepted_at'];
                $arr['time_from_start'] = $station['time_from_start'];
                array_push($data, $arr);
            } 
            PartnerTripStation::insert($data);
        } catch (\Exception $e) {
            throw new \Exception('We could not able to insert these stations.' . $e->getMessage());
        }
    
        return true;
    }

    public function assignUser($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $input = collect($args)->except('directive')->toArray();

        try {
            $userStation = PartnerTripUser::where('trip_id', $args['trip_id'])
                ->where('user_id', $args['user_id'])->firstOrFail();
            $userStation->update(['station_id' => $args['station_id']]);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('No subscription for the provided user ID.');
        }
 
        return [
            "status" => true,
            "message" => "You've successfully assigned to this station."
        ];
    }

    public function unassignUser($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            PartnerTripUser::where('station_id', $args['station_id'])
                ->where('user_id', $args['user_id'])->delete();
        } catch (\Exception $e) {
            throw new \Exception('User station assignment cancellation faild.');
        }
 
        return [
            "status" => true,
            "message" => "You've successfully unassigned from this station."
        ];
    }

    public function acceptStation($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $station = PartnerTripStation::where('id', $args['station_id'])->firstOrFail();
            $station->update(['state' => 'PICKABLE', 'accepted_at' => now()]);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Station with the provided ID is not found.');
        }

        try {
            $userCurrentStation = PartnerTripUser::where('trip_id', $args['trip_id'])
                ->where('user_id', $station['created_by'])
                ->firstOrFail();
            $userCurrentStation->update(['station_id' => $args['station_id']]);
        } catch (ModelNotFoundException $e) { 
            PartnerTripUser::create([
                'trip_id' => $args['trip_id'],
                'station_id' => $args['station_id'],
                'user_id' => $station['created_by'],
                'subscription_verified_at' => now()
            ]);
        }

        return $station;
    }
}
