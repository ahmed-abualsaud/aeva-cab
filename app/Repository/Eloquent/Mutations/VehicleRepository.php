<?php

namespace App\Repository\Eloquent\Mutations;

use App\Vehicle;
use App\Traits\HandleUpload;
use App\Repository\Eloquent\BaseRepository;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VehicleRepository extends BaseRepository
{
    use HandleUpload;

    public function __construct(Vehicle $model)
    {
        parent::__construct($model);
    }

    public function create(array $args)
    {
        $input = collect($args)->except(['directive', 'photo'])->toArray();

        if (array_key_exists('photo', $args) && $args['photo']) {
          $url = $this->uploadOneFile($args['photo'], 'images');
          $input['photo'] = $url;
        }
        
        $vehicle = $this->model->create($input);

        return $vehicle;
    }

    public function update(array $args)
    {
        $input = collect($args)->except(['id', 'directive', 'photo'])->toArray();

        try {
            $vehicle = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.vehicle_not_found'));
        }

        if (array_key_exists('photo', $args) && $args['photo']) {
            if ($vehicle->photo) $this->deleteOneFile($vehicle->photo, 'images');
            $url = $this->uploadOneFile($args['photo'], 'images');
            $input['photo'] = $url;
        }

        $vehicle->update($input);

        return $vehicle;
    }

    public function activateVehicle(array $args) {
        DB::table('driver_vehicles')
            ->where('driver_vehicles.driver_id', $args['driver_id'])
            ->where('active', true)
            ->update(['active' => false]);

        $vehicle = $this->model->join('driver_vehicles', 'driver_vehicles.vehicle_id', '=', 'vehicles.id')
        ->where('driver_vehicles.driver_id', $args['driver_id'])
        ->where('driver_vehicles.vehicle_id', $args['vehicle_id']);

        $vehicle->update(['active' => true]);
        return $vehicle->first();
    }
}
