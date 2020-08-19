<?php

namespace App\GraphQL\Queries;

use App\Vehicle;
use App\CarModel;
use App\DriverVehicle;

class VehicleResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function driverAssignedVehicles($_, array $args) 
    {
        $driverVehicles = DriverVehicle::where('driver_id', $args['driver_id'])->pluck('vehicle_id');

        return Vehicle::whereIn('id', $driverVehicles)->get();
    }

    public function vehicleModelsByType($_, array $args)
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
