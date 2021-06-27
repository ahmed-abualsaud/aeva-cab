<?php

namespace App\GraphQL\Mutations;

use JWTAuth;
use App\Role;
use App\Admin;

class RoleResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function update($_, array $args)
    {
        $input = collect($args)->except(['id', 'directive'])->toArray();

        try {
            $role = Role::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.role_not_found'));
        }

        $this->invalidateTokens($args['id']);

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
