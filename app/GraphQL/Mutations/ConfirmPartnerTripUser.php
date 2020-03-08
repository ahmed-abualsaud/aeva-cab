<?php

namespace App\GraphQL\Mutations;

use App\PartnerTrip;
use App\User;
use App\PartnerTripUser;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Exceptions\CustomException;

class ConfirmPartnerTripUser
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
        // Check that subscription code is valid.
        $trip = PartnerTrip::where('subscription_code', $args['subscription_code'])->first();

        if (!$trip) {
            throw new CustomException(
                'Confirmation Faild',
                'The provided subscription code is invalid.',
                'Trip Confirmation'
            );
        }
        
        // Check that user is not subscribed for this trip.
        $tripUser = PartnerTripUser::where('partner_trip_id', $trip['id'])
            ->where('partner_user_id', $args['user_id'])->first();
        
        if (!$tripUser) {
            PartnerTripUser::create([
                'partner_trip_id' => $trip['id'],
                'partner_user_id' => $args['user_id'],
                'subscription_verified_at' => now()
            ]);
            User::where('id', $args['user_id'])->update(['partner_id' => $trip['partner_id']]);
        } else if ($tripUser && !$tripUser->subscription_verified_at) {
            $tripUser->update(['subscription_verified_at' => now()]);
        } else {
            throw new CustomException(
                'Confirmation Faild',
                'You have already been subscribed for this trip.',
                'Trip Confirmation'
            );
        }
        
        return $trip;
    }
}
