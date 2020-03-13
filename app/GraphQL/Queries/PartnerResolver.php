<?php

namespace App\GraphQL\Queries;

use App\Driver;
use App\PartnerDriver;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class PartnerResolver
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
    public function drivers($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $partnerDrivers = PartnerDriver::where('partner_id', $args['partner_id'])->get()->pluck('driver_id');

        if ($args['assigned']) {
            $drivers = Driver::whereIn('id', $partnerDrivers)->get();
        } else {
            $drivers = Driver::whereNotIn('id', $partnerDrivers)->get();
        }

        return $drivers;
    }
}
