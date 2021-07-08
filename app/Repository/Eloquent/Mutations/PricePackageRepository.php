<?php

namespace App\Repository\Eloquent\Mutations;

use App\PricePackage;
use App\Traits\HandleUpload;
use App\Repository\Eloquent\BaseRepository;

class PricePackageRepository extends BaseRepository 
{
    use HandleUpload;

    public function __construct(PricePackage $model)
    {
        parent::__construct($model);
    }

    public function create(array $args)
    {
        try {
            $input = collect($args)->except(['directive', 'photo'])->toArray();
    
            if (array_key_exists('photo', $args) && $args['photo']) {
              $url = $this->uploadOneFile($args['photo'], 'images');
              $input['photo'] = $url;
            }
            
            $pricePackage = $this->model->create($input);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.create_price_failed'));
        }

        return $pricePackage;
    }

    public function update(array $args)
    {
        try {
            $input = collect($args)->except(['id', 'directive', 'photo'])->toArray();
            $pricePackage = $this->model->findOrFail($args['id']);

            if (array_key_exists('photo', $args) && $args['photo']) {
                if ($pricePackage->photo) $this->deleteOneFile($pricePackage->photo, 'images');
                $url = $this->uploadOneFile($args['photo'], 'images');
                $input['photo'] = $url;
            }
    
            $pricePackage->update($input);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.update_price_failed'));
        }

        return $pricePackage;
    }
}
