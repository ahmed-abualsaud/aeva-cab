<?php

namespace App\GraphQL\Mutations;

use \App\Fleet;
use \App\Traits\HandleUpload;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class FleetResolver
{ 
    use HandleUpload;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        $fleetInput = collect($args)->except(['directive', 'avatar'])->toArray();

        if (array_key_exists('avatar', $args) && $args['avatar']) {
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $fleetInput['avatar'] = $driverInput['avatar'] = $url;
        }
        
        $fleet = Fleet::create($fleetInput);

        return $fleet;
    }

    public function update($_, array $args)
    {
        $input = collect($args)->except(['id', 'directive', 'avatar'])->toArray();

        try {
            $fleet = Fleet::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('The provided fleet ID is not found.');
        }

        if (array_key_exists('avatar', $args) && $args['avatar']) {
            if ($fleet->avatar) $this->deleteOneFile($fleet->avatar, 'avatars');
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }

        $fleet->update($input);

        return $fleet;
    }
}
