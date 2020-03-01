<?php

namespace App\GraphQL\Queries;

use App\User;
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
        $status = $args['status'];

        switch($status) {
            case 'subscribed':
                $partnerTripUsers = PartnerTripUser::where('partner_trip_id', $args['trip_id'])
                    ->whereNotNull('subscription_verified_at')
                    ->get()->pluck('partner_user_id');

                $users = User::where('partner_id', $args['partner_id'])
                    ->whereIn('id', $partnerTripUsers)->get();
                break;
            case 'notSubscribed':
                $partnerTripUsers = PartnerTripUser::where('partner_trip_id', $args['trip_id'])
                    ->get()->pluck('partner_user_id');

                $users = User::where('partner_id', $args['partner_id'])
                    ->whereNotIn('id', $partnerTripUsers)->get();
                break;
            case 'notVerified':
                $partnerTripUsers = PartnerTripUser::where('partner_trip_id', $args['trip_id'])
                    ->whereNull('subscription_verified_at')
                    ->get()->pluck('partner_user_id');

                $users = User::where('partner_id', $args['partner_id'])
                    ->whereIn('id', $partnerTripUsers)->get();
                break;
        }

        return $users;
    }
}
