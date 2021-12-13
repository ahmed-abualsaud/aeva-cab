<?php

namespace App\Repository\Eloquent\Controllers;

use JWTAuth;
use App\User;
use Illuminate\Support\Str;
use App\Traits\HandleUpload;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;

class UserRepository extends Controller
{
    use HandleUpload;

    private $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function handleAvatar(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric',
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $user = $this->model->findOrFail($request->id);
        } catch (ModelNotFoundException $e) {
            return response()->json(__('lang.user_not_found'), 500);
        }

        if ($user->avatar) $this->deleteOneFile($user->avatar, 'avatars');
        $url = $this->uploadOneFile($request->avatar, 'avatars');

        $user->update(['avatar' => $url]);

        return response()->json($user);
    }

    public function getLanguage(Request $request) 
    {
        $request->session()->forget('locale');
        return __('auth.failed');
    }

    public function create(Request $request)
    {
        $args = $request->all();
        $input = collect($args)
            ->except(['directive', 'avatar', 'platform', 'ref_code', 'trip_id', 'payable'])
            ->toArray(); 

        if (array_key_exists('avatar', $args) && $args['avatar']) {
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }
        
        if (array_key_exists('password', $input)) {
            $password = $input['password'];
        } elseif (array_key_exists('phone', $input)) {
            $password = $input['phone'];
            $input['phone_verified_at'] = date('Y-m-d H:i:s');
        } else {
            throw new CustomException(__('lang.password_phone_not_provided'));
        }
        $input['password'] = Hash::make($password);

        $user = $this->model->create($input);

        $user->update(["ref_code" => Hashids::encode($user->id)]);

        if (array_key_exists('ref_code', $args) && $args['ref_code']) {
            $referrer_id = Hashids::decode($args['ref_code']);
            if ($referrer_id && isset($referrer_id[0]) && is_int($referrer_id[0])) {
                $referrer = $this->model->find($referrer_id[0]);
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
                $this->createSubscription($args, $user->id);
        } else {
            Auth::onceUsingId($user->id);
            $token = JWTAuth::fromUser($user);
        }

        return [
            "access_token" => $token,
            "user" => $user
        ];
    }

    public function login(Request $request)
    {
        $args = $request->all();
        $emailOrPhone = filter_var($args['emailOrPhone'], FILTER_VALIDATE_EMAIL);
        $credentials = [];

        if ($emailOrPhone) {
            $credentials["email"] = $args['emailOrPhone'];
        } else {
            $credentials["phone"] = $args['emailOrPhone'];
        } 

        $credentials["password"] = $args['password'];  

        if (! $token = auth('user')->attempt($credentials)) {
            throw new CustomException(__('lang.invalid_auth_credentials'));
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

    public function socialLogin(Request $request)
    {
        $args = $request->all();
        try {
            if (array_key_exists('platform', $args) 
                && $args['platform'] == 'android' 
                && $args['provider'] == 'google') 
            {
                $args['token'] = Socialite::driver('google')
                    ->getAccessTokenResponse($args['token'])['access_token'];
            }
            $userData = Socialite::driver($args['provider'])->userFromToken($args['token']);
            $input = ['provider' => $args['provider']];
            if ($args['provider'] == 'apple') {
                $input['provider_id'] = $userData->id;
                $input['name'] = $userData->name ?? 'Apple User';
                $input['email'] = $userData->email;
            } else {
                $input['provider_id'] = $userData->getId();
                $input['name'] = $userData->getName();
                $input['email'] = $userData->getEmail();
                $input['avatar'] = $userData->getAvatar();
            }
        } catch (\Exception $e) {
            throw new CustomException(__('lang.invalid_token'));
        }

        $updateData = [];

        try {
            $user = $this->model->where('provider', Str::lower($args['provider']))
                ->where('provider_id', $input['provider_id'])
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            try {
                $user = $this->model->create($input);
                $updateData['ref_code'] = Hashids::encode($user->id);
            } catch (QueryException $e) {
                $user = $this->model->where('email', $input['email'])
                    ->first();
            }
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
}
