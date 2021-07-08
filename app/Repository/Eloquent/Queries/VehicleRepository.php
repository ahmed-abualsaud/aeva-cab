<?php

namespace App\Repository\Eloquent\Queries;

use App\CarModel;
use App\Repository\Queries\VehicleRepositoryInterface;
use App\Repository\Eloquent\BaseRepository;

class VehicleRepository extends BaseRepository implements VehicleRepositoryInterface
{
    public function __construct(CarModel $model)
    {
        parent::__construct($model);
    }

    public function typeModels(array $args)
    {
        $models = $this->model->where('car_models.type_id', $args['type_id'])
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
