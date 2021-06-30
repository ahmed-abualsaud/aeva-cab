<?php

namespace App\GraphQL\Mutations;

use App\CarModel;
use App\Traits\HandleUpload;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CarModelResolver 
{
    use HandleUpload;
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        $input = collect($args)->except(['directive', 'photo'])->toArray();

        if (array_key_exists('photo', $args) && $args['photo']) {
          $url = $this->uploadOneFile($args['photo'], 'images');
          $input['photo'] = $url;
        }
        
        $carModel = CarModel::create($input);

        return $carModel;
    }

    public function update($_, array $args)
    {
        $input = collect($args)->except(['id', 'directive', 'photo'])->toArray();

        try {
            $carModel = CarModel::findOrFail($args['id']);
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