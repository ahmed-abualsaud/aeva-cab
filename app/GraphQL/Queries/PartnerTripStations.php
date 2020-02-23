<?php

namespace App\GraphQL\Queries;

use App\PartnerTripStation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class PartnerTripStations
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
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $status = $args['status'];

        switch($status) {
            case 'accepted':
                $stations = PartnerTripStation::where('partner_trip_id', $args['partner_trip_id'])
                    ->whereNotNull('accepted_at')->get();
                break;
            case 'notAccepted':
                $stations = PartnerTripStation::where('partner_trip_id', $args['partner_trip_id'])
                    ->whereNull('accepted_at')->get();
                break;
        }

        return $stations;
    }
}
