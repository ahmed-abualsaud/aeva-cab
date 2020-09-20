<?php

namespace App\GraphQL\Mutations;

use \App\Vehicle;
use \App\Traits\Uploadable;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VehicleResolver 
{
    use Uploadable;
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function create($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $input = collect($args)->except(['directive', 'photo'])->toArray();

        if (array_key_exists('photo', $args) && $args['photo']) {
          $url = $this->uploadOneFile($args['photo'], 'images');
          $input['photo'] = $url;
        }
        
        $vehicle = Vehicle::create($input);

        return $vehicle;
    }

    public function update($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $input = collect($args)->except(['id', 'directive', 'photo'])->toArray();

        try {
            $vehicle = Vehicle::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('The provided vehicle ID is not found.');
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