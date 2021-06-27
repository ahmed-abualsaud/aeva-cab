<?php

namespace App\GraphQL\Mutations;

use JWTAuth;
use App\Admin;
use App\Traits\HandleUpload;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Hash;

class AdminResolver
{
    use HandleUpload;
     /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        $input = collect($args)->except(['directive'])->toArray();
        $input['password'] = Hash::make($input['phone']);

        if (array_key_exists('avatar', $args) && $args['avatar']) {
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }
         
        $admin = Admin::create($input);

        return $admin;
    }

    public function update($_, array $args)
    {
        $input = collect($args)->except(['id', 'directive', 'avatar'])->toArray();

        try {
            $admin = Admin::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.admin_not_found') );
        }

        if (array_key_exists('avatar', $args) && $args['avatar']) {
            if ($admin->avatar) 
                $this->deleteOneFile($admin->avatar, 'avatars');
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }

        if (array_key_exists('role_id', $args) && $args['role_id']) {
            $this->invalidateToken($admin);
        }

        $admin->update($input);

        return $admin;
    }

    public function login($_, array $args)
    {
        $emailOrPhone = filter_var($args['emailOrPhone'], FILTER_VALIDATE_EMAIL);
        $credentials = [];

        if ($emailOrPhone) {
            $credentials["email"] = $args['emailOrPhone'];
        } else {
            $credentials["phone"] = $args['emailOrPhone'];
        } 

        $credentials["password"] = $args['password'];

        if (! $token = auth('admin')->attempt($credentials)) {
            throw new CustomException(
                __('lang.invalid_auth_credentials'),
                'customValidation'
            );
        }

        $admin = auth('admin')->user();

        $admin->update(['token' => $token]);

        return [
            'access_token' => $token,
            'admin' => $admin
        ];
    }

    public function updatePassword($_, array $args)
    {
        try {
            $admin = Admin::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.admin_not_found'));
        }

        if (!(Hash::check($args['current_password'], $admin->password))) {
            throw new CustomException(
                __('lang.password_missmatch'),
                'customValidation'
            );
        }

        if (strcmp($args['current_password'], $args['new_password']) == 0) {
            throw new CustomException(
                __('lang.type_new_password'),
                'customValidation'
            );
        }

        $admin->password = Hash::make($args['new_password']);
        $admin->save();

        return __('lang.password_changed');
    }

    protected function invalidateToken($admin)
    {
        if ($admin->token) {
            JWTAuth::setToken($admin->token)->invalidate();
            $admin->update(['token' => null]);
        }
    }
}
