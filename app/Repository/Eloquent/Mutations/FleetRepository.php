<?php

namespace App\Repository\Eloquent\Mutations;

use \App\Fleet;
use \App\Traits\HandleUpload;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repository\Eloquent\BaseRepository;

class FleetRepository extends BaseRepository
{
    use HandleUpload;

    public function __construct(Fleet $model)
    {
        parent::__construct($model);
    }

    public function create(array $args)
    {
        $fleetInput = collect($args)->except(['directive', 'avatar'])->toArray();

        if (array_key_exists('avatar', $args) && $args['avatar']) {
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $fleetInput['avatar'] = $driverInput['avatar'] = $url;
        }
        
        $fleet = $this->model->create($fleetInput);

        return $fleet;
    }

    public function update(array $args)
    {
        $input = collect($args)->except(['id', 'directive', 'avatar'])->toArray();

        try {
            $fleet = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.fleet_not_found'));
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
