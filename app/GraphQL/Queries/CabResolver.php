<?php

namespace App\GraphQL\Queries;

use App\UserRequest;
use App\UserRequestPayment;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CabResolver
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
    public function requestHistory($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $requests = UserRequest::query();

        if (array_key_exists('status', $args) && $args['status']) {
            $requests->where('status', $args['status']);
        }

        $requests->orderBy('created_at', 'DESC');
        $requests = $requests->get();
        
        $response = [
            "requests" => $requests,
            "count" => $requests->count(),
        ];

        return $response;
    }

    public function requestStatement($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $requestCount = UserRequest::count();

        $earning = UserRequestPayment::selectRaw('
            SUM(ROUND(fixed) + ROUND(distance)) as overall_earning,
            SUM(ROUND(commission)) as overall_commission,
            SUM(ROUND(driver_pay)) as driver_earning,
            SUM(ROUND(driver_commission)) as driver_commission
        ')
        ->first();
        
        $response = [
            "count" => $requestCount,
            "overallEarning" => $earning->overall_earning,
            "driverEarning" => $earning->driver_earning,
            "overallCommission" => $earning->overall_commission,
            "driverCommission" => $earning->driver_commission
        ];

        return $response;
    }
}
