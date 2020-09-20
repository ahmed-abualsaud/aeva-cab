<?php

namespace App\GraphQL\Queries;

use App\CabRequest;
use App\CabRequestPayment;
use App\Traits\Filterable;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CabResolver
{
    use Filterable;
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function requestStatement($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $requestCount = CabRequest::query();

        $requestGroup = CabRequest::selectRaw('
            DATE(created_at) as date,
            count(*) as count
        ');

        $requestPaymentGroup = CabRequestPayment::selectRaw('
            DATE(created_at) as date,
            SUM(ROUND(fixed) + ROUND(distance)) as overallEarning,
            SUM(ROUND(commission)) as overallCommission
        ');
            

        $statement = CabRequestPayment::selectRaw('
            SUM(ROUND(fixed) + ROUND(distance)) as overallEarning,
            SUM(ROUND(commission)) as overallCommission,
            SUM(ROUND(driver_pay)) as driverEarning,
            SUM(ROUND(driver_commission)) as driverCommission
        '); 

        if (array_key_exists('period', $args) && $args['period']) {
            $statement = $this->dateFilter($args['period'], $statement, 'created_at');
            $requestCount = $this->dateFilter($args['period'], $requestCount, 'created_at');
            $requestGroup = $this->dateFilter($args['period'], $requestGroup, 'created_at');
            $requestPaymentGroup = $this->dateFilter($args['period'], $requestPaymentGroup, 'created_at');
        }

        $statement = $statement->first();
        $requestCount = $requestCount->count();
        $requestGroup = $requestGroup->groupBy('date')->get();
        $requestPaymentGroup = $requestPaymentGroup->groupBy('date')->get();
        
        $response = [
            "count" => $requestCount,
            "overallEarning" => $statement->overallEarning,
            "driverEarning" => $statement->driverEarning,
            "overallCommission" => $statement->overallCommission,
            "driverCommission" => $statement->driverCommission,
            "requests" => $requestGroup,
            "requestPayments" => $requestPaymentGroup
        ];

        return $response;
    }
}
