<?php

namespace App\GraphQL\Mutations;

use App\Role;
use App\Traits\UploadFile;
use App\Exceptions\CustomException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class RoleResolver
{
    use UploadFile;
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
         
        $role = Role::create($input);

        return $role;
    }

    public function update($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $input = collect($args)->except(['id', 'directive', 'avatar'])->toArray();

        try {
            $role = Role::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('The provided role ID is not found.');
        }

        if (array_key_exists('avatar', $args) && $args['avatar']) {
            if ($role->avatar) $this->deleteOneFile($role->avatar, 'avatars');
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }

        $role->update($input);

        return $role;
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

        if (! $token = auth('role')->attempt($credentials)) {
        throw new CustomException(
            'The provided authentication credentials are invalid.',
            'customValidation'
        );
        }

        $role = auth('role')->user();

        return [
            'access_token' => $token,
            'role' => $role
        ];
    }
}
