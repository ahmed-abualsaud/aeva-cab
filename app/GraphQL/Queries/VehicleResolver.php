<?php

namespace App\GraphQL\Queries;

use App\CarModel;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class VehicleResolver
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
    public function vehicleModelsByType($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $models = CarModel::where('car_models.type_id', $args['type_id'])
            ->join('car_makes', 'car_makes.id', '=', 'car_models.make_id')
            ->selectRaw("
                car_models.id AS id, 
                CONCAT(car_makes.name, ' ', car_models.name) AS name,
                car_models.seats,
                car_models.photo
            ")
            ->get();

        return $models;

        /** 
         * Another indirect way to get the models by type
         * 
        $models = Vehicle::where('vehicles.car_type_id', $args['type_id'])
            ->join('car_makes', 'car_makes.id', '=', 'vehicles.car_make_id')
            ->join('car_models', 'car_models.id', '=', 'vehicles.car_model_id')
            ->selectRaw("
                vehicles.car_model_id AS id, 
                CONCAT(car_makes.name, ' ', car_models.name) AS name,
                car_models.seats,
                car_models.photo
            ")
            ->groupBy('vehicles.car_model_id', 'name')
            ->get();
        */
    }
}
