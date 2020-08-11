<?php

namespace App\GraphQL\Mutations;

use Carbon\Carbon;
use App\BusinessTrip;
use App\BusinessTripUser;
use App\BusinessTripStation;
use App\Exceptions\CustomException;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BusinessTripStationResolver
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
                array_push($data, $arr);
            } 
            BusinessTripStation::insert($data);
        } catch (\Exception $e) {
            throw new \Exception('We could not able to insert these stations.' . $e->getMessage());
        }

        return BusinessTripStation::where('trip_id', $args['trip_id'])
            ->get();
    }

    public function update($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $input = collect($args)->except(['id', 'directive', 'trip_id', 'state'])->toArray();

        try {
            $station = BusinessTripStation::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('The provided station ID is not found.');
        }

        if (array_key_exists('state', $args) && $args['state'] && $args['state'] != $station->state) {
            $input['state'] = $args['state'];
            $updatedStation = BusinessTripStation::where('state', $args['state'])
                ->where('trip_id', $args['trip_id'])
                ->where('id', '<>', $station->id)
                ->first();
            if ($updatedStation) $updatedStation->update(['state' => $station->state]);
        }

        $station->update($input);

        return $station;
    }

    public function updateRoute($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            
            $cases = []; $ids = []; $distance = []; $duration = [];

            foreach ($args['stations'] as $value) {
                $id = (int) $value['id'];
                $cases[] = "WHEN {$id} then ?";
                $distance[] = $value['distance'];
                $duration[] = $value['duration'];
                $ids[] = $id;
            }

            $ids = implode(',', $ids);
            $cases = implode(' ', $cases);
            $params = array_merge($distance, $duration);
            $params[] = Carbon::now();

            \DB::update("UPDATE `business_trip_stations` SET 
                `distance` = CASE `id` {$cases} END, 
                `duration` = CASE `id` {$cases} END, 
                `updated_at` = ? 
                WHERE `id` in ({$ids})", $params);

            $total = end($args['stations']);

            BusinessTrip::find($args['trip_id'])
                ->update(['distance' => $total['distance'], 'duration' => $total['duration']]);

            return true;
            
        } catch (\Exception $e) {
            throw new \Exception('Could not able to update. '.$e->getMessage());
        }
    }

    public function assignUser($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $input = collect($args)->except('directive')->toArray();

        try {
            $userStation = BusinessTripUser::where('trip_id', $args['trip_id'])
                ->where('user_id', $args['user_id'])->firstOrFail();
            $userStation->update(['station_id' => $args['station_id']]);
        } catch (ModelNotFoundException $e) {
            throw new CustomException('No subscription for the provided user ID.');
        }
 
        return [
            "status" => true,
            "message" => "You've successfully assigned to this station."
        ];
    }

    public function unassignUser($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            BusinessTripUser::where('station_id', $args['station_id'])
                ->where('user_id', $args['user_id'])->delete();
        } catch (\Exception $e) {
            throw new CustomException('User station assignment cancellation faild.');
        }
 
        return [
            "status" => true,
            "message" => "You've successfully unassigned from this station."
        ];
    }

    public function acceptStation($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $station = BusinessTripStation::where('id', $args['station_id'])->firstOrFail();
            $station->update(['state' => 'PICKABLE', 'accepted_at' => now()]);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Station with the provided ID is not found.');
        }

        try {
            $userCurrentStation = BusinessTripUser::where('trip_id', $args['trip_id'])
                ->where('user_id', $station['created_by'])
                ->firstOrFail();
            $userCurrentStation->update(['station_id' => $args['station_id']]);
        } catch (ModelNotFoundException $e) { 
            BusinessTripUser::create([
                'trip_id' => $args['trip_id'],
                'station_id' => $args['station_id'],
                'user_id' => $station['created_by'],
                'subscription_verified_at' => now()
            ]);
        }

        return $station;
    }
}
