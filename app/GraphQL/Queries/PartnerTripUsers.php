<?php

namespace App\GraphQL\Queries;

use App\PartnerUser;
use App\PartnerTripUser;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class PartnerTripUsers
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
        $partnerTripUsers = PartnerTripUser::where('partner_trip_id', $args['tripID'])->get()->pluck('partner_user_id');

        if ($args['subscribed']) {
            $users = PartnerUser::where('partner_id', $args['partnerID'])
                ->whereIn('id', $partnerTripUsers)->get();
        } else {
            $users = PartnerUser::where('partner_id', $args['partnerID'])
                ->whereNotIn('id', $partnerTripUsers)->get();
        }

        return $users;
    }
}
