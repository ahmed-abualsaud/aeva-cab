<?php

namespace App\GraphQL\Mutations;

use App\Exceptions\CustomException;
use Illuminate\Support\Arr;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class QruzMemberLogin
{
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
    public function resolve($rootValue, array $args, GraphQLContext $context = null, ResolveInfo $resolveInfo)
    {
        $credentials = Arr::only($args, ['email', 'password']);

        if (! $token = auth('role')->attempt($credentials)) {
        throw new CustomException(
            'Authentication Faild',
            'The provided authentication credentials are invalid.',
            'Authentication'
        );
        }

        $role = auth('role')->user();
        
        $response['access_token'] = $token;
        $response['role'] = $role;

        return $response;
    }
}
