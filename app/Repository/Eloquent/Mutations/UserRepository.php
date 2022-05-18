<?php

namespace App\Repository\Eloquent\Mutations;

use App\User;
use App\PartnerUser;
use App\Jobs\SendOtp;
use App\BusinessTripSubscription;
use Illuminate\Support\Str;
use App\Traits\HandleUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repository\Eloquent\BaseRepository;
use App\Repository\Mutations\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    use HandleUpload;

    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function handleAvatar(array $args)
    {
        try {
            $user = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new CustomException(__('lang.user_not_found'));
        }

        if ($user->avatar) $this->deleteOneFile($user->avatar, 'avatars');
        $url = $this->uploadOneFile($args['avatar'], 'avatars');

        $user->update(['avatar' => $url]);

        return $user;
    }

    public function getLanguage(Request $request) 
    {
        $request->session()->forget('locale');
        return __('auth.failed');
    }

    public function create(array $args)
    {
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
            auth('user')->onceUsingId($user->id);
            $token = auth('user')->fromUser($user);
        }

        return [
            "access_token" => $token,
            "user" => $user
        ];
    }

    public function createMultipleUsers(array $args)
    {
        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            return $this->createUsersAndAssociatePartner($args);
        } else {
            return $this->createUsers($args);
        }
    }

    public function update(array $args)
    {
        $input = collect($args)->except(['id', 'directive', 'avatar'])->toArray();

        try {
            $user = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new CustomException(__('lang.user_not_found'));
        }

        if (array_key_exists('avatar', $args) && $args['avatar']) {
            if ($user->avatar) $this->deleteOneFile($user->avatar, 'avatars');
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }

        if (array_key_exists('phone', $args) && $args['phone'] && is_null($user->phone)) {
            $input['phone_verified_at'] = date('Y-m-d H:i:s');
        }

        $user->update($input);

        return $user;
    }

    public function login(array $args)
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

    public function socialLogin(array $args)
    {
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

        auth('user')->onceUsingId($user->id);

        $token = auth('user')->fromUser($user);

        return [
            'access_token' => $token,
            'user' => $user
        ];
    }

    public function phoneVerification(array $args)
    {
        $verification_code = mt_rand(1000, 9999);

        $message = __('lang.verification_code', [
            'verification_code' => $verification_code,
            'signature' => config('custom.otp_signature'),
        ]);

        if (array_key_exists('verify', $args) && $args['verify'] && 
            $this->model->where('phone', $args['phone'])->exists()) {
                throw new CustomException( __('lang.user_exists'));        
        }

        SendOtp::dispatch($args['phone'], $message);

        return [
            "verificationCode" => $verification_code
        ];
    }

    public function updatePassword(array $args)
    {
        try {
            $user = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.user_not_found'));
        }

        if (!(Hash::check($args['current_password'], $user->password))) {
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

        $user->password = Hash::make($args['new_password']);
        $user->save();

        return [
            'status' => true,
            'message' => __('lang.password_changed')
        ];

    }

    public function destroy(array $args)
    {
        return $this->model->whereIn('id', $args['id'])->delete();
    }

    protected function createUsersAndAssociatePartner(array $args)
    {
        DB::beginTransaction();
        try {
            $this->createUsers($args);
            $users = $this->getPartnerUsers($args['partner_id']);
            $this->createPartnerUsers($users, $args['partner_id']);
            if (array_key_exists('trip_id', $args) && $args['trip_id'])
                $this->createSubscriptions($users, $args);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new CustomException(__('lang.create_user_failed'));
        }
    }

    protected function createUsers(array $args)
    {
        try {
            $arr = ['created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];

            if (array_key_exists('partner_id', $args))
                $arr['partner_id'] = $args['partner_id'];

            foreach($args['users'] as $user) {
                $arr['name'] = $user['name'];
                $arr['phone'] = $user['phone'];
                $arr['password'] = Hash::make($user['phone']);
                $data[] = $arr;
            }
            $this->model->insertOrIgnore($data); 
        } catch (\Exception $e) {
            throw new CustomException(__('lang.create_user_failed'));
        }
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
        PartnerUser::insertOrIgnore($partnerUserData); 
    }

    protected function createSubscription($args, $user_id)
    {
        BusinessTripSubscription::create([
            'trip_id' => $args['trip_id'],
            'user_id' => $user_id,
            'subscription_verified_at' => date('Y-m-d H:i:s'),
            'due_date' =>  date('Y-m-d'),
            'payable' => $args['payable']
        ]);
    }

    protected function createSubscriptions(array $users, array $args)
    {
        $tripUserArr = [
            'trip_id' => $args['trip_id'], 
            'subscription_verified_at' => date('Y-m-d H:i:s'),
            'due_date' =>  date('Y-m-d'),
            'payable' => $args['payable']
        ];

        foreach($users as $user) {
            $tripUserArr['user_id'] = $user;
            $tripUserData[] = $tripUserArr;
        }
        BusinessTripSubscription::insertOrIgnore($tripUserData);
    }

    protected function getPartnerUsers(int $partner_id)
    {
        return $this->model->select('id')
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
