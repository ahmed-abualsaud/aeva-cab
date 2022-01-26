<?php

namespace App\Repository\Eloquent\Mutations;

use JWTAuth;
use App\Role;
use App\Admin;
use App\Repository\Eloquent\BaseRepository;

class RoleRepository extends BaseRepository
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function update(array $args)
    {
        $input = collect($args)->except(['id', 'directive'])->toArray();

        try {
            $role = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.role_not_found'));
        }

        $role->update($input);

        return $role;
    }

    protected function invalidateTokens(int $id)
    {
        $admin = Admin::select('token')
            ->where('role_id', $id)
            ->where('id', '<>', auth('admin')->user()->id);

        $tokens = $admin->pluck('token')
            ->filter()
            ->flatten()
            ->toArray();

        if ($tokens) {
            foreach($tokens as $token) {
                JWTAuth::setToken($token)->invalidate();
            }
            $admin->update(['token' => null]);
        }
    }
}
