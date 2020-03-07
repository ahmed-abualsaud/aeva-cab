<?php

namespace App\GraphQL\Mutations;

use App\PartnerTripUser;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Exceptions\CustomException;

class CreatePartnerTripUser
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
        $data = [];
        $arr = [];

        foreach($args['partner_user_id'] as $val) {
            $arr['partner_trip_id'] = $args['partner_trip_id'];
            $arr['partner_user_id'] = $val;

            array_push($data, $arr);
        } 

        try {
            PartnerTripUser::insert($data);
        } catch (\Exception $e) {
            throw new CustomException(
              'Subscription faild.',
              'Each user is allowed to subscribe for a trip once.',
              'Integrity constraint violation.'
            );
        }

        return [
            "status" => "SUCCESS",
            "message" => "Subscription code has been sent."
        ];
    }
}
