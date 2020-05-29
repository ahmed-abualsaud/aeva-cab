<?php

namespace App\GraphQL\Mutations;

use App\OndemandRequest;
use App\OndemandRequestVehicle;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OndemandRequestResolver
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
        try {
            $input = collect($args)->except(['directive', 'vehicles'])->toArray();
            $request = OndemandRequest::create($input);

            $data = array(); 
            $arr = array();

            foreach($args['vehicles'] as $vehicle) {
                $arr['request_id'] = $request->id;
                $arr['car_type_id'] = $vehicle['car_type_id'];
                $arr['car_model_id'] = $vehicle['car_model_id'];
                $arr['count'] = $vehicle['count'];
                array_push($data, $arr);
            } 

            $vehicles = OndemandRequestVehicle::insert($data);
        } catch (\Exception $e) {
            throw new \Exception('We could not able to create this request.' . $e->getMessage());
        }
        

        return $request;
    }
}
