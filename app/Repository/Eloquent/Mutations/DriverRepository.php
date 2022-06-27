<?php

namespace App\Repository\Eloquent\Mutations;

use App\Driver;
use App\Vehicle;
use App\Document;
use App\DriverStats;
use App\DriverVehicle;
use App\PartnerDriver;

use App\Jobs\SendOtp;

use App\Traits\HandleUpload;
use App\Traits\HandleAccessTokenCache;

use App\Exceptions\CustomException;
use App\Events\DriverLocationUpdated;

use Vinkla\Hashids\Facades\Hashids;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repository\Eloquent\BaseRepository;
use App\Repository\Mutations\DriverRepositoryInterface;

class DriverRepository extends BaseRepository implements DriverRepositoryInterface
{
    use HandleUpload;
    use HandleAccessTokenCache;

    public function __construct(Driver $model)
    {
        parent::__construct($model);
    }

    public function handleAvatar(array $args)
    {
        try {
            $driver = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new CustomException(__('lang.driver_not_found'));
        }

        if ($driver->avatar) $this->deleteOneFile($driver->avatar, 'avatars');
        $url = $this->uploadOneFile($args['avatar'], 'avatars');

        $driver->update(['avatar' => $url]);

        return $driver;
    }

    public function create(array $args)
    {
        $input = collect($args)->except(['directive', 'avatar', 'text'])->toArray();

        if (array_key_exists('password', $input) && $input['password']) {
            $password = $input['password'];
        } else {
            $password = Hashids::encode(rand(10000000000,1000000000000));
        }
        $input['password'] = Hash::make($password);
        $input['status'] = true;
 
        if (array_key_exists('avatar', $args) && $args['avatar']) {
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }

        $driver = $this->model->create($input);

        $wallet = 0;
        if(array_key_exists('wallet', $args) && $args['wallet']) {
            $wallet = $args['wallet'];
        }

        DriverStats::create(['driver_id' => $driver->id, 'wallet' => $wallet]);

        $driver->update(["ref_code" => Hashids::encode($driver->id)]);

        if (array_key_exists('ref_code', $args) && $args['ref_code']) {
            $referrer_id = Hashids::decode($args['ref_code']);
            if ($referrer_id && isset($referrer_id[0]) && is_int($referrer_id[0])) {
                $referrer = $this->model->find($referrer_id[0]);
                if ($referrer) {
                    $driver->referrer_id = $referrer->id;
                    $driver->save();
                }
            }
        }

        auth('driver')->onceUsingId($driver->id);
        $driver->token = auth('driver')->fromUser($driver);

        //$verification_code = mt_rand(1000, 9999);
        $verification_code = '0000';

        $message = __('lang.verification_code', [
            'verification_code' => $verification_code,
            'signature' => config('custom.otp_signature'),
        ]);
        
        SendOtp::dispatch($args['phone'], $message);

        $driver->verification_code = $verification_code;

        $vehicle = Vehicle::create([
            'approved' => false,
            'text' => $args['text']
        ]);

        DriverVehicle::create([
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'active' => false
        ]);

        Document::createDriverDocuments($driver->id);
        Document::createVehicleDocuments($vehicle->id);
        
        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            $this->createPartnerDriver($args['partner_id'], $driver->id);
        }

        $driver->password = $password;
        $driver->wallet = $wallet;
        $driver->cab_status = 'Offline';
        return $driver;
    }

    public function update(array $args)
    {
        $input = collect($args)->except(['id', 'directive', 'avatar'])->toArray();

        try {
            $driver = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new CustomException(__('lang.driver_not_found'));
        }

        if (array_key_exists('avatar', $args) && $args['avatar']) {
            if ($driver->avatar) $this->deleteOneFile($driver->avatar, 'avatars');
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }

        $driver->update($input);

        if ((array_key_exists('latitude', $args) && $args['latitude']) &&
        (array_key_exists('longitude', $args) && $args['longitude'])) {
            broadcast(new DriverLocationUpdated($args['id'], [
                'lat' => $args['latitude'], 
                'lng' => $args['longitude']
            ]));
        }

        return $driver;
    }

    public function login(array $args)
    {
        try {
            $driver = Driver::where('phone', $args['phone'])->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.driver_not_found'));
        }

        $token = $this->getCachedToken('driver', $driver->id);
        if ($token) {
            $this->invalidateToken('driver', $token);
        }

        $credentials["phone"] = $args['phone'];
        $credentials["password"] = $args['password'];

        if (!$token = auth('driver')->attempt($credentials)) {
            throw new CustomException(__('lang.invalid_auth_credentials'));
        }

        $driver = auth('driver')->user();

        if (!$driver->status) {
            throw new CustomException(__('lang.your_account_is_disabled'));
        }

        if (!$driver->phone_verified_at) 
        {
            //$verification_code = mt_rand(1000, 9999);
            $verification_code = '0000';

            $message = __('lang.verification_code', [
                'verification_code' => $verification_code,
                'signature' => config('custom.otp_signature'),
            ]);
            
            SendOtp::dispatch($args['phone'], $message);

            $driver->verification_code = $verification_code;
        }

        if (array_key_exists('device_id', $args) 
            && $args['device_id'] 
            && $driver->device_id != $args['device_id']) 
        {
            $driver->update(['device_id' => $args['device_id']]);
        }

        return [
            'access_token' => $token,
            'driver' => $driver
        ];
    }

    public function updatePassword(array $args)
    {
        try {
            $driver = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.driver_not_found'));
        }

        if (!(Hash::check($args['current_password'], $driver->password))) {
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

        $driver->password = Hash::make($args['new_password']);
        $driver->save();

        return [
            'status' => true,
            'message' => __('lang.password_changed')
        ];

    }

    public function assignVehicle(array $args)
    { 
        try {
            DriverVehicle::create([
                'vehicle_id' => $args['vehicle_id'],
                'driver_id' => $args['driver_id'],
                'active' => false
            ]);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.assignment_failed'));
        }
 
        return [
            "status" => true,
            "message" => __('lang.assign_vehicle')
        ];
    }

    public function unassignVehicle(array $args)
    {
        try {
            DriverVehicle::where('driver_id', $args['driver_id'])
                ->where('vehicle_id', $args['vehicle_id'])
                ->delete();
        } catch (\Exception $e) {
            throw new CustomException(__('lang.assign_cancel_failed') . $e->getMessage());
        }

        return [
            "status" => true,
            "message" => __('lang.unassign_vehicle')
        ];
    }

    public function destroy(array $args)
    {
        return $this->model->whereIn('id', $args['id'])->delete();
    }

    protected function createPartnerDriver($partner_id, $driver_id)
    {
        PartnerDriver::create([
            "partner_id" => $partner_id,
            "driver_id" => $driver_id
        ]);
    }
}
