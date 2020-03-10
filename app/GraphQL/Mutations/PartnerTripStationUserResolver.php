<?php

namespace App\GraphQL\Mutations;

use App\PartnerTripStationUser;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Exceptions\CustomException;

class PartnerTripStationUserResolver
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
        $input = collect($args)->except('directive')->toArray();

        try {
            PartnerTripStationUser::create($input);
        } catch (\Exception $e) {
            throw new CustomException(
              'Assignment faild.',
              'Each user is allowed to be assigned to one station for each trip.',
              'Integrity constraint violation.'
            );
        }
 
        return [
            "status" => true,
            "message" => "You've successfully assigned to this station."
        ];
    }

    public function destroy($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            PartnerTripStationUser::where('station_id', $args['station_id'])
                ->where('user_id', $args['user_id'])->delete();
        } catch (\Exception $e) {
            throw new CustomException(
                'User station assignment cancellation faild.',
                'Something went wrong',
                'Unknown.'
            );
        }
 
        return [
            "status" => true,
            "message" => "You've successfully unassigned from this station."
        ];
    }
}
