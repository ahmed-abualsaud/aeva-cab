<?php

namespace App\GraphQL\Queries;

use \App\TripLog;
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
    public function getDriverLocation($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $location = TripLog::select(['latitude', 'longitude'])
                ->where('trip_id', $args['trip_id'])
                ->whereDate('created_at', now()->toDateString())
                ->latest()
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new \Exception('No data for the provided trip ID at this moment. ' . $e->getMessage());
        }

        return [
            'latitude' => $location->latitude,
            'longitude' => $location->longitude
        ];
    }
}
