<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use JWTAuth;

class UserResolver
{
    
    public function create($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = new User();
        $input = collect($args)->except('directive')->toArray();
        $input['password'] = Hash::make($input['password']);
        $user->fill($input);
        $user->save();

        $token = null;

        if (!array_key_exists('partner_id', $args)) {
            Auth::onceUsingId($user->id);
            $token = JWTAuth::fromUser($user);
        }
        

        return [
            "access_token" => $token,
            "user" => $user
        ];
    }

    public function update($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $input = collect($args)->except(['id', 'directive'])->toArray();

        try {
            $user = User::findOrFail($args['id']);
            $user->update($input);
        } catch (\Exception $e) {
            throw new CustomException(
                'User Not Updated.',
                'The provided user ID is not found.',
                'ModelNotFound.'
            );
        }

        return ['user' => $user];
    }

    public function login($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {

        $credentials = collect($args)->only(['email', 'password'])->toArray();

        if (! $token = auth('user')->attempt($credentials)) {
            throw new CustomException(
                'Authentication Faild',
                'The provided authentication credentials are invalid.',
                'Authentication'
            );
        }

        $user = auth('user')->user();

        return [
            'access_token' => $token,
            'user' => $user
        ];
    }

    public function socialLogin($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
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

    public function phoneVerification($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $verification_code = '';
        for($i = 0; $i < 4; $i++) {
            $verification_code .= mt_rand(0, 9);
        }

        return [
            "verificationCode" => $verification_code
        ];
    }

    public function updatePassword($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $user = User::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            return [
                'status' => false, 
                'message' => 'The provided user ID is not found.'
            ];
        }

        if (!(Hash::check($args['current_password'], $user->password))) {
            return [
                'status' => false,
                'message' => 'Your current password does not matches with the password you provided.'
            ];
        }

        if (strcmp($args['current_password'], $args['new_password']) == 0) {
            return [
                'status' => false,
                'message' => 'New Password cannot be same as your current password. Please choose a different password.'
            ];
        }

        $user->password = Hash::make($args['new_password']);
        $user->save();

        return [
            'status' => true,
            'message' => 'Password changed successfully.'
        ];

    }
}