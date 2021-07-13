<?php

namespace App\Repository\Eloquent\Mutations;

use App\CarType;
use App\Traits\HandleUpload;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repository\Eloquent\BaseRepository;

class CarTypeRepository extends BaseRepository
{
    use HandleUpload;

    public function __construct(CarType $model)
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
        
        $carType = $this->model->create($input);

        return $carType;
    }

    public function update(array $args)
    {
        $input = collect($args)->except(['id', 'directive', 'photo'])->toArray();

        try {
            $carType = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.car_type_not_found'));
        }

        if (array_key_exists('photo', $args) && $args['photo']) {
            if ($carType->photo) $this->deleteOneFile($carType->photo, 'images');
            $url = $this->uploadOneFile($args['photo'], 'images');
            $input['photo'] = $url;
        }

        $carType->update($input);

        return $carType;
    }
}
