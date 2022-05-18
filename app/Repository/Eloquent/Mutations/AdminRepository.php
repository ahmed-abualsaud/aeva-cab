<?php

namespace App\Repository\Eloquent\Mutations;

use App\Admin;
use App\Traits\HandleUpload;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Hash;
use App\Traits\HandleAccessTokenCache;
use App\Repository\Eloquent\BaseRepository;

class AdminRepository extends BaseRepository
{
    use HandleUpload;
    use HandleAccessTokenCache;
    
    public function __construct(Admin $model)
    {
        parent::__construct($model);
    }

    public function create(array $args)
    {
        $input = collect($args)->except(['directive'])->toArray();
        $input['password'] = Hash::make($input['phone']);
        $input['status'] = true;

        if (array_key_exists('avatar', $args) && $args['avatar']) {
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }
         
        $admin = $this->model->create($input);

        return $admin;
    }

    public function update(array $args)
    {
        $input = collect($args)->except(['id', 'directive', 'avatar'])->toArray();

        try {
            $admin = $this->model->findOrFail($args['id']);
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
            $token = $this->getCachedToken('admin', $admin->id);
            if ($token)
                $this->invalidateToken('admin', $token);
        }

        $admin->update($input);

        return $admin;
    }

    public function login(array $args)
    {
        $isEmail = filter_var($args['emailOrPhone'], FILTER_VALIDATE_EMAIL);
        $credentials = [];

        if ($isEmail)
            $credentials["email"] = $args['emailOrPhone'];
        else
            $credentials["phone"] = $args['emailOrPhone'];

        $credentials["password"] = $args['password'];

        if (! $token = auth('admin')->attempt($credentials)) {
            throw new CustomException(
                __('lang.invalid_auth_credentials'),
                'customValidation'
            );
        }

        $admin = auth('admin')->user();

        if (!$admin->status) {
            throw new CustomException(__('lang.your_account_is_disabled'));
        }

        $this->handleAccessTokenCache('admin', $admin, $token);

        return [
            'access_token' => $token,
            'admin' => $admin
        ];
    }

    public function updatePassword(array $args)
    {
        try {
            $admin = $this->model->findOrFail($args['id']);
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

        auth('admin')->onceUsingId($admin->id);

        $token = auth('admin')->fromUser($admin);
        
        $this->handleAccessTokenCache('admin', $admin, $token);

        return [
            'access_token' => $token,
            'admin' => $admin
        ];
    }
}
