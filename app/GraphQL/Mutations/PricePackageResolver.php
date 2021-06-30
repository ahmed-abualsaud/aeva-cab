<?php

namespace App\GraphQL\Mutations;

use App\PricePackage;
use App\Traits\HandleUpload;

class PricePackageResolver
{
    use HandleUpload;
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        try {
            $input = collect($args)->except(['directive', 'photo'])->toArray();
    
            if (array_key_exists('photo', $args) && $args['photo']) {
              $url = $this->uploadOneFile($args['photo'], 'images');
              $input['photo'] = $url;
            }
            
            $pricePackage = PricePackage::create($input);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.create_price_failed'));
        }

        return $pricePackage;
    }

    public function update($_, array $args)
    {
        try {
            $input = collect($args)->except(['id', 'directive', 'photo'])->toArray();
            $pricePackage = PricePackage::findOrFail($args['id']);

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
