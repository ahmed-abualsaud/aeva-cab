<?php

namespace App\GraphQL\Mutations;

use JWTAuth;
use App\User;
use App\DeviceToken;
use App\PartnerUser;
use App\Jobs\SendOtp;
use App\Traits\UploadFile;
use Illuminate\Support\Str; 
use App\Exceptions\CustomException;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use GraphQL\Type\Definition\ResolveInfo;
use Laravel\Socialite\Facades\Socialite;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Database\Eloquent\ModelNotFoundException;
 
class UserResolver
{
    use UploadFile;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        $input = collect($args)
            ->except(['directive', 'avatar', 'platform', 'device_id', 'ref_code'])
            ->toArray(); 

        if (array_key_exists('avatar', $args) && $args['avatar']) {
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }
        
        if (array_key_exists('password', $input)) {
            $password = $input['password'];
        } elseif (array_key_exists('phone', $input)) {
            $password = $input['phone'];
        } else {
            throw new CustomException('Password or phone is required but not provided.');
        }
        $input['password'] = Hash::make($password);

        $user = User::create($input);

        $user->update(["ref_code" => Hashids::encode($user->id)]);

        if (array_key_exists('ref_code', $args) && $args['ref_code']) {
            $referrer_id = Hashids::decode($args['ref_code']);
            if ($referrer_id && isset($referrer_id[0]) && is_int($referrer_id[0])) {
                $referrer = User::find($referrer_id[0]);
                if ($referrer) {
                    $referrer->wallet_balance += 15;
                    $user->referrer_id = $referrer->id;
                    $user->wallet_balance += 15;
                    $referrer->save();
                    $user->save();
                }
            }
        }

        if (array_key_exists('device_id', $args) && array_key_exists('platform', $args)) {
            $this->createDeviceToken($_, $args, $user->id);
        }

        $token = null;
        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            PartnerUser::create([
                "partner_id" => $args['partner_id'],
                "user_id" => $user->id
            ]);
        } else {
            Auth::onceUsingId($user->id);
            $token = JWTAuth::fromUser($user);
        }

        return [
            "access_token" => $token,
            "user" => $user
        ];
    }

    public function update($_, array $args)
    {
        $input = collect($args)->except(['id', 'directive', 'avatar'])->toArray();

        try {
            $user = User::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new CustomException('The provided user ID is not found.');
        }

        if (array_key_exists('avatar', $args) && $args['avatar']) {
            if ($user->avatar) $this->deleteOneFile($user->avatar, 'avatars');
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }

        if (array_key_exists('phone', $args) && $args['phone'] && is_null($user->phone)) {
            $input['phone_verified_at'] = now();
        }

        $user->update($input);

        return ['user' => $user];
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

        if (! $token = auth('user')->attempt($credentials)) {
            throw new CustomException('The provided authentication credentials are invalid.');
        }

        $user = auth('user')->user();

        if (array_key_exists('device_id', $args) && array_key_exists('platform', $args)) {
            try {
                DeviceToken::where('device_id', $args['device_id'])
                    ->where('tokenable_type', 'App\User')
                    ->firstOrFail();
            } catch (ModelNotFoundException $e) {
                $this->createDeviceToken($_, $args, $user->id);
            }
        }

        return [
            'access_token' => $token,
            'user' => $user
        ];
    } 

    public function socialLogin($_, array $args)
    {
        try {
            $userData = Socialite::driver($args['provider'])->userFromToken($args['token']);
        } catch (\Exception $e) {
            throw new CustomException('The provided token is invalid.');
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
            $user->update(["ref_code" => Hashids::encode($user->id)]);
        }

        if (array_key_exists('device_id', $args) && array_key_exists('platform', $args)) {
            try {
                DeviceToken::where('device_id', $args['device_id'])
                    ->where('tokenable_type', 'App\User')
                    ->firstOrFail();
            } catch (ModelNotFoundException $e) {
                $this->createDeviceToken($_, $args, $user->id);
            }
        }

        Auth::onceUsingId($user->id);

        $token = JWTAuth::fromUser($user);

        return [
            'access_token' => $token,
            'user' => $user
        ];
    }

    public function phoneVerification($_, array $args)
    {
        $verification_code = mt_rand(1000, 9999);

        $message = $verification_code . " is your Qruz verification code";

        SendOtp::dispatch($args['phone'], $message);

        return [
            "verificationCode" => $verification_code
        ];
    }

    public function updatePassword($_, array $args)
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

    protected function createDeviceToken($_, $args, $user_id)
    {
        $tokenInput = collect($args)->only(['platform', 'device_id'])->toArray();
        $tokenInput['tokenable_id'] = $user_id;
        $tokenInput['tokenable_type'] = 'App\User';
        DeviceToken::create($tokenInput);
    }
}