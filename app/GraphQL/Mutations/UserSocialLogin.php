<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use JWTAuth;

class UserSocialLogin
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
    try {
      $userData = Socialite::driver($args['provider'])->userFromToken($args['token']);
    } catch (\Exception $e) {
      throw new CustomException(
        'Authentication Faild',
        'The provided token is invalid.',
        'Authentication'
      );
    }

    try {
      $user = User::where('provider', Str::lower($args['provider']))->where('provider_id', $userData->getId())->firstOrFail();
    } catch (ModelNotFoundException $e) {
      $user = User::create([
        'name'        => $userData->getName(),
        'email'       => $userData->getEmail(),
        'provider'    => $args['provider'],
        'provider_id' => $userData->getId(),
        'avatar'      => $userData->getAvatar(),
      ]);
    }
    
    Auth::onceUsingId($user->id);

    $token = JWTAuth::fromUser($user);

    return [
      'access_token' => $token,
      'user' => $user
    ];

  }
}