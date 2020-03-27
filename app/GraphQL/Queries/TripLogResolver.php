<?php

namespace App\GraphQL\Queries;

use \App\TripLog;
use \App\PartnerTripUser;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TripLogResolver
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
    public function driverLocation($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $location = TripLog::select(['latitude', 'longitude'])
                ->where('log_id', $args['log_id'])
                ->latest()
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new \Exception('No data for the provided trip log ID.');
        }

        return [
            'latitude' => $location->latitude,
            'longitude' => $location->longitude
        ];
    }

    public function pickedUsers($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $users = TripLog::where('log_id', $args['log_id'])
            ->where('status', 'PICKED_UP')
            ->join('users', 'users.id', '=', 'trip_logs.user_id')
            ->select('users.*')
            ->get();

        return $users;
    }

    public function pickedAndNotpickedUsers($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $users = PartnerTripUser::where('station_id', $args['station_id'])
            ->join('users', 'users.id', '=', 'partner_trip_users.user_id')
            ->leftJoin('trip_logs', function ($join) use ($args) {
                $join->on('users.id', '=', 'trip_logs.user_id')
                    ->where('trip_logs.log_id', $args['log_id'])
                    ->where('status', 'PICKED_UP');
            })
            ->selectRaw('users.*, (CASE WHEN trip_logs.status IS NULL THEN 0 ELSE 1 END) AS is_picked_up
            ')
            ->get();

        return $users;
    }

    public function arrivedAndNotArrivedUsers($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $users = PartnerTripUser::where('station_id', $args['station_id'])
            ->join('users', 'users.id', '=', 'partner_trip_users.user_id')
            ->leftJoin('trip_logs', function ($join) use ($args) {
                $join->on('users.id', '=', 'trip_logs.user_id')
                    ->where('trip_logs.log_id', $args['log_id'])
                    ->where('status', 'USER_ARRIVED');
            })
            ->selectRaw('users.*, (CASE WHEN trip_logs.status IS NULL THEN 0 ELSE 1 END) AS is_arrived
            ')
            ->get();

        return $users;
    }

}
