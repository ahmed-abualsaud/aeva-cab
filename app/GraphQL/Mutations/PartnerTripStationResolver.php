<?php

namespace App\GraphQL\Mutations;

use App\PartnerTripStation;
use App\PartnerTripStationUser;
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
    public function assignUser($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $input = collect($args)->except('directive')->toArray();

        try {
            PartnerTripStationUser::create($input);
        } catch (\Exception $e) {
            throw new \Exception('Each user is allowed to be assigned to one station for each trip.');
        }
 
        return [
            "status" => true,
            "message" => "You've successfully assigned to this station."
        ];
    }

    public function unassignUser($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            PartnerTripStationUser::where('station_id', $args['station_id'])
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
            $station->update(['accepted_at' => now()]);
        } catch (\Exception $e) {
            throw new \Exception('No station with the provided ID is found.');
        }

        try {
            $userCurrentStation = PartnerTripStationUser::where('trip_id', $args['trip_id'])->where('user_id', $station['created_by'])->firstOrFail();
            $userCurrentStation->update(['station_id' => $args['station_id']]);
        } catch (ModelNotFoundException $e) {
            PartnerTripStationUser::create([
                'trip_id' => $args['trip_id'],
                'station_id' => $args['station_id'],
                'user_id' => $station['created_by']
            ]);
        }

        return [
            "status" => true,
            "message" => "Selected station has been accepted."
        ];
    }
}
