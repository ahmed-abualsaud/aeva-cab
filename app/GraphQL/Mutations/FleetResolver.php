<?php

namespace App\GraphQL\Mutations;

use \App\Fleet;
use \App\Driver;
use \App\Traits\UploadFile;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Facades\Hash;

class FleetResolver
{ 
    use UploadFile;

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
        $fleetInput = collect($args)->except(['directive', 'avatar'])->toArray();
        $driverInput = collect($args)->only(['name', 'email', 'phone'])->toArray();
        $driverInput['password'] = Hash::make($driverInput['phone']);

        if ($args['avatar']) {
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $fleetInput['avatar'] = $driverInput['avatar'] = $url;
        }
        
        $fleet = Fleet::create($fleetInput);

        $driverInput['fleet_id'] = $fleet->id;
        Driver::create($driverInput);

        return $fleet;
    }

    public function update($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $input = collect($args)->except(['id', 'directive', 'avatar'])->toArray();

        try {
            $fleet = Fleet::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('The provided fleet ID is not found.');
        }

        if ($args['avatar']) {
            if ($fleet->avatar) $this->deleteOneFile($fleet->avatar, 'avatars');
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }

        $fleet->update($input);

        return $fleet;
    }
}
