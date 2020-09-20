<?php

namespace App\GraphQL\Mutations;

use App\Admin;
use App\Traits\Uploadable;
use App\Exceptions\CustomException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AdminResolver
{
    use Uploadable;
    /**
     * @param $rootValue
     * @param array                                                    $args
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext|null $context
     * @param \GraphQL\Type\Definition\ResolveInfo                     $resolveInfo
     *
     * @throws \Exception
     *
     * @return array
     */
    public function create($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
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

    public function update($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $input = collect($args)->except(['id', 'directive', 'avatar'])->toArray();

        try {
            $admin = Admin::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('The provided admin ID is not found.');
        }

        if (array_key_exists('avatar', $args) && $args['avatar']) {
            if ($admin->avatar) $this->deleteOneFile($admin->avatar, 'avatars');
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }

        $admin->update($input);

        return $admin;
    }

    public function login($rootValue, array $args, GraphQLContext $context = null, ResolveInfo $resolveInfo)
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
                'The provided authentication credentials are invalid.',
                'customValidation'
            );
        }

        $admin = auth('admin')->user();

        return [
            'access_token' => $token,
            'admin' => $admin
        ];
    }

    public function updatePassword($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $admin = Admin::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('The provided admin ID is not found.');
        }

        if (!(Hash::check($args['current_password'], $admin->password))) {
            throw new CustomException(
                'Your current password does not matches with the password you provided.',
                'customValidation'
            );
        }

        if (strcmp($args['current_password'], $args['new_password']) == 0) {
            throw new CustomException(
                'New Password cannot be same as your current password. Please choose a different password.',
                'customValidation'
            );
        }

        $admin->password = Hash::make($args['new_password']);
        $admin->save();

        return 'Password changed successfully.';
    }
}
