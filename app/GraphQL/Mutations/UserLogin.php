<?php

namespace App\GraphQL\Mutations;

use App\Exceptions\CustomException;
use Illuminate\Support\Arr;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UserLogin
{
  /**
   * Return a value for the field.
   *
   * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
   * @param  mixed[]  $args The arguments that were passed into the field.
   * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
   * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
   * @return mixed
   */
  public function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
  {
    
    $credentials = Arr::only($args, ['email', 'password']);

    if (! $token = auth('user')->attempt($credentials)) {
      throw new CustomException(
        'Authentication Faild',
        'The provided authentication credentials are invalid.',
        'Authentication'
      );
    }

    $user = auth('user')->user();
    
    $response['access_token'] = $token;
    $response['user'] = $user;

    return $response;

  }
}