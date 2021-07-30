<?php

namespace App\Repository\Eloquent\Mutations;

use App\Supervisor;
use \App\Traits\HandleUpload;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repository\Eloquent\BaseRepository;

class SupervisorRepository extends BaseRepository
{
    use HandleUpload;

    public function __construct(Supervisor $model)
    {
        parent::__construct($model);
    }

    public function create(array $args)
    {
        $input = collect($args)->except(['directive', 'avatar'])->toArray();

        if (array_key_exists('avatar', $args) && $args['avatar']) {
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }
        
        $supervisor = $this->model->create($input);

        return $supervisor;
    }

    public function update(array $args)
    {
        $input = collect($args)->except(['id', 'directive', 'avatar'])->toArray();

        try {
            $supervisor = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.supervisor_not_found'));
        }

        if (array_key_exists('avatar', $args) && $args['avatar']) {
            if ($supervisor->avatar) $this->deleteOneFile($supervisor->avatar, 'avatars');
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }

        $supervisor->update($input);

        return $supervisor;
    }
}