<?php

namespace App\Repository\Eloquent\Queries;

use App\Vehicle;
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

    public function activeVehicle(array $args) 
    {   
        return Vehicle::join('driver_vehicles', 'driver_vehicles.vehicle_id', '=', 'vehicles.id')
            ->where('driver_vehicles.driver_id', $args['driver_id'])
            ->where('driver_vehicles.active', true)
            ->first();
    }
}
