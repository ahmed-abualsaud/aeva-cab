<?php

namespace App\Repository\Eloquent\Mutations;

use \App\Vehicle;
use \App\Traits\HandleUpload;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repository\Eloquent\BaseRepository;

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
}
