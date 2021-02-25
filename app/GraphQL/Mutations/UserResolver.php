<?php

namespace App\GraphQL\Mutations;

use JWTAuth;
use App\User;
use App\PartnerUser;
use App\Jobs\SendOtp;
use App\BusinessTripUser;
use Illuminate\Support\Str;
use App\Traits\HandleUpload;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Database\Eloquent\ModelNotFoundException;
 
class UserResolver
{
    use HandleUpload;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        $input = collect($args)
            ->except(['directive', 'avatar', 'platform', 'ref_code', 'trip_id'])
            ->toArray(); 

        if (array_key_exists('avatar', $args) && $args['avatar']) {
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }
        
        if (array_key_exists('password', $input)) {
            $password = $input['password'];
        } elseif (array_key_exists('phone', $input)) {
            $password = $input['phone'];
            $input['phone_verified_at'] = now();
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

        $token = null;
        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            $this->createPartnerUser($args['partner_id'], $user->id);
            if (array_key_exists('trip_id', $args) && $args['trip_id'])
                $this->subscribeUser($args['trip_id'], $user->id);
        } else {
            Auth::onceUsingId($user->id);
            $token = JWTAuth::fromUser($user);
        }

        return [
            "access_token" => $token,
            "user" => $user
        ];
    }

    public function createMultipleUsers($_, array $args)
    {
        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            return $this->createUsersAndAssociatePartner($args);
        } else {
            return $this->createUsers($args);
        }
    }

    protected function createUsersAndAssociatePartner(array $args)
    {
        DB::beginTransaction();
        try {
            $this->createUsers($args);
            $users = $this->getPartnerUsers($args['partner_id']);
            $this->createPartnerUsers($users, $args['partner_id']);
            if (array_key_exists('trip_id', $args) && $args['trip_id'])
                $this->subscribeUsers($users, $args['trip_id']);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new CustomException('Duplicate users');
        }
    }

    protected function createUsers(array $args)
    {
        try {
            $arr = ['created_at' => now(), 'updated_at' => now()];

            if (array_key_exists('partner_id', $args))
                $arr['partner_id'] = $args['partner_id'];

            foreach($args['users'] as $user) {
                $arr['name'] = $user['name'];
                $arr['phone'] = $user['phone'];
                $arr['password'] = Hash::make($user['phone']);
                $data[] = $arr;
            }
            User::insert($data); 
        } catch (\Exception $e) {
            throw new CustomException('Duplicate users');
        }
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

        $updateData = [];

        if (!$user->ref_code) {
            $updateData['ref_code'] = Hashids::encode($user->id);
        }

        if (array_key_exists('device_id', $args) 
            && $args['device_id'] 
            && $user->device_id != $args['device_id']) 
        {
            $updateData['device_id'] = $args['device_id'];
        }

        if ($updateData) $user->update($updateData);

        return [
            'access_token' => $token,
            'user' => $user
        ];
    } 

    public function socialLogin($_, array $args)
    {
        try {
            if (array_key_exists('platform', $args) && $args['platform'] == 'android' && $args['provider'] == 'google') {
                $args['token'] = Socialite::driver('google')
                    ->getAccessTokenResponse($args['token'])['access_token'];
            }
            $userData = Socialite::driver($args['provider'])->userFromToken($args['token']);
            $input = ['provider' => $args['provider']];
            if ($args['provider'] == 'apple') {
                $input['provider_id'] = $userData->id;
                $input['name'] = $userData->name ?? explode('@', $userData->email)[0];
                $input['email'] = $userData->email;
            } else {
                $input['provider_id'] = $userData->getId();
                $input['name'] = $userData->getName();
                $input['email'] = $userData->getEmail();
                $input['avatar'] = $userData->getAvatar();
            }
        } catch (\Exception $e) {
            throw new CustomException('The provided token is invalid.');
        }

        $updateData = [];

        try {
            $user = User::where('provider', Str::lower($args['provider']))
                ->where('provider_id', $input['provider_id'])->firstOrFail();
        } catch (ModelNotFoundException $e) {
            $user = User::create($input);
            $updateData['ref_code'] = Hashids::encode($user->id);
        }

        if (array_key_exists('device_id', $args) 
            && $args['device_id'] 
            && $user->device_id != $args['device_id']) 
        {
            $updateData['device_id'] = $args['device_id'];
        }

        if ($updateData) $user->update($updateData);

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

        $message = "Your Qruz code is: ".$verification_code;

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

    public function destroy($_, array $args)
    {
        return User::whereIn('id', $args['id'])->delete();
    }

    protected function createPartnerUser($partner_id, $user_id)
    {
        PartnerUser::create([
            "partner_id" => $partner_id,
            "user_id" => $user_id
        ]);
    }

    protected function createPartnerUsers(array $users, int $partner_id)
    {
        $partnerUserArr = ['partner_id' => $partner_id];
        foreach($users as $user) {
            $partnerUserArr['user_id'] = $user;
            $partnerUserData[] = $partnerUserArr;
        }
        PartnerUser::insert($partnerUserData); 
    }

    protected function subscribeUser($trip_id, $user_id)
    {
        BusinessTripUser::create([
            'trip_id' => $trip_id,
            'user_id' => $user_id,
            'subscription_verified_at' => now()
        ]);
    }

    protected function subscribeUsers(array $users, int $trip_id)
    {
        $tripUserArr = ['trip_id' => $trip_id, 'subscription_verified_at' => now()];
        foreach($users as $user) {
            $tripUserArr['user_id'] = $user;
            $tripUserData[] = $tripUserArr;
        }
        BusinessTripUser::insert($tripUserData);
    }

    protected function getPartnerUsers(int $partner_id)
    {
        return User::select('id')
            ->where('partner_id', $partner_id)
            ->whereNotIn('id', function($query) use ($partner_id) {
                $query->select('user_id')
                    ->from('partner_users')
                    ->where('partner_id', $partner_id);
            })
            ->pluck('id')
            ->toArray();
    }
}