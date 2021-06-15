<?php

namespace App\GraphQL\Queries;

use App\CarModel;

class VehicleResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function typeModels($_, array $args)
    {
        $models = CarModel::where('car_models.type_id', $args['type_id'])
            ->isPublic($args)
            ->join('car_makes', 'car_makes.id', '=', 'car_models.make_id')
            ->selectRaw("
                car_models.id AS id, 
                CONCAT(car_makes.name, ' ', car_models.name) AS name,
                car_models.seats,
                car_models.photo
            ")
            ->get();

        return $models;
    }
}
