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
        $input = collect($args)->except(['directive', 'avatar', 'national_id'])->toArray();

        if (array_key_exists('avatar', $args) && $args['avatar']) {
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }

        if (array_key_exists('national_id', $args) && $args['national_id']) {
            $url = $this->uploadOneFile($args['national_id'], 'documents');
            $input['national_id'] = $url;
        }
        
        $supervisor = $this->model->create($input);

        return $supervisor;
    }

    public function update(array $args)
    {
        $input = collect($args)->except(['id', 'directive', 'avatar', 'national_id'])->toArray();

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

        if (array_key_exists('national_id', $args) && $args['national_id']) {
            if ($supervisor->national_id) $this->deleteOneFile($supervisor->national_id, 'documents');
            $url = $this->uploadOneFile($args['national_id'], 'documents');
            $input['national_id'] = $url;
        }

        $supervisor->update($input);

        return $supervisor;
    }

    public function destroy(array $args)
    {
        return $this->model->whereIn('id', $args['id'])->delete();
    }
}