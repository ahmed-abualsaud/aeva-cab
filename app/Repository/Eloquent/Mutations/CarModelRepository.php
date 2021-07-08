<?php

namespace App\Repository\Eloquent\Mutations;

use App\CarModel;
use App\Traits\HandleUpload;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repository\Eloquent\BaseRepository;

class CarModelRepository extends BaseRepository
{
    use HandleUpload;

    public function __construct(CarModel $model)
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
        
        $carModel = $this->model->create($input);

        return $carModel;
    }

    public function update(array $args)
    {
        $input = collect($args)->except(['id', 'directive', 'photo'])->toArray();

        try {
            $carModel = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.car_model_not_found'));
        }

        if (array_key_exists('photo', $args) && $args['photo']) {
            if ($carModel->photo) $this->deleteOneFile($carModel->photo, 'images');
            $url = $this->uploadOneFile($args['photo'], 'images');
            $input['photo'] = $url;
        }

        $carModel->update($input);

        return $carModel;
    }
}
