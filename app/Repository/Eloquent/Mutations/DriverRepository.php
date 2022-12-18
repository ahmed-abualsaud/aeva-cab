<?php

namespace App\Repository\Eloquent\Mutations;

use App\Driver;
use App\Helpers\TraceEvents;
use App\Vehicle;
use App\Document;
use App\DriverStats;
use App\DriverVehicle;
use App\PartnerDriver;

use App\Jobs\SendOtp;

use App\Traits\HandleUpload;
use App\Traits\HandleAccessTokenCache;

use App\Exceptions\CustomException;

use Aeva\Cab\Domain\Models\CabRequest;
use Aeva\Cab\Domain\Traits\CabRequestHelper;

use Vinkla\Hashids\Facades\Hashids;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repository\Eloquent\BaseRepository;
use App\Repository\Mutations\DriverRepositoryInterface;

class DriverRepository extends BaseRepository implements DriverRepositoryInterface
{
    use HandleUpload;
    use HandleAccessTokenCache;
    use CabRequestHelper;

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

        if (config('custom.send_otp')) {
            $verification_code = mt_rand(1000, 9999);;
        } else {
            $verification_code = '0000';
        }

        $input['otp'] = $verification_code;

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
        $input = collect($args)->except(['id', 'directive', 'avatar', 'secondary_phone', 'request_id', 'national_id'])->toArray();

        if (array_key_exists('secondary_phone', $args) && $args['secondary_phone']) {
            $input['secondary_phone'] = $args['secondary_phone'];
        }

        try {
            $driver = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new CustomException(__('lang.driver_not_found'));
        }

        if (array_key_exists('national_id', $args) && $args['national_id']) {
            $input['national_id'] = $args['national_id'];
        }

        if (array_key_exists('phone', $args) && $args['phone'] && is_null($driver->phone)) {
            $input['phone_verified_at'] = date('Y-m-d H:i:s');
        }

        if (array_key_exists('avatar', $args) && $args['avatar']) {
            if ($driver->avatar) $this->deleteOneFile($driver->avatar, 'avatars');
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }

        if (array_key_exists('active_status',$args) && $args['active_status']){

            if ($args['active_status'] == 'Suspended') {
                if (!(array_key_exists('suspension_till',$args) && $args['suspension_till'])){
                    throw new CustomException('suspension_till is required');
                }
                $input['suspended_at'] = date('Y-m-d H:i:s');
            }

            if (in_array($args['active_status'] , ['Blocked', 'Suspended'])) {
                ! is_null(@auth('driver')->user()) and trace(TraceEvents::LOG_OUT);
                $this->logOutOldDevices('driver',$driver->id);
            }

            if (in_array($args['active_status'] , ['Active', 'Blocked'])) {
                $input['suspended_at'] = null;
                $input['suspension_till'] = null;
                $input['suspension_reason'] = null;
            }

            if (in_array($args['active_status'] , ['Active', 'Suspended'])) {
                $input['block_reason'] = null;
            }
        }

         if (array_key_exists('cab_status', $args) && $args['cab_status']) {
            $active_requests = CabRequest::driverLive(['driver_id' => $args['id']])->first();
            if($active_requests && in_array($args['cab_status'], ['Offline', 'Online'])) {
                throw new CustomException(__('lang.update_status_failed').' id = '.$active_requests->id);
            }
            $this->updateDriverStatus($args['id'], $args['cab_status']);
         }

        $driver->update($input);

        return $driver;
    }

    public function login(array $args)
    {
        try {
            $driver = Driver::where('phone', $args['phone'])->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new CustomException(__('lang.driver_not_found'));
        }

        if ($driver->active_status == 'Blocked') {
            throw new CustomException(__('lang.your_account_is_disabled'));
        }

        if ($driver->active_status == 'Suspended') {
            if (time() < strtotime($driver->suspension_till)) {
                throw new CustomException(__('lang.your_account_is_still_suspended'));
            } else {
                $driver->update([
                    'active_status' => 'Active',
                    'suspended_at' => null,
                    'suspension_till' => null,
                    'suspension_reason' => null
                ]);
            }
        }

        $credentials["phone"] = $args['phone'];
        $credentials["password"] = $args['password'];

        if (!$token = auth('driver')->attempt($credentials)) {
            throw new CustomException(__('lang.invalid_auth_credentials'));
        }

        $driver = auth('driver')->user();

        if (!$driver->phone_verified_at)
        {
            if (config('custom.send_otp')) {
                $verification_code = mt_rand(1000, 9999);;
            } else {
                $verification_code = '0000';
            }

            $driver->update(['otp' => $verification_code]);

            $message = __('lang.verification_code', [
                'verification_code' => $verification_code,
                'signature' => config('custom.otp_signature'),
            ]);

            SendOtp::dispatch($args['phone'], $message);
        }

        if (array_key_exists('device_id', $args)
            && $args['device_id']
            && $driver->device_id != $args['device_id'])
        {
            $driver->update(['device_id' => $args['device_id']]);
        }

        $driver->verification_code = @$verification_code;

        try {
            $this->handleAccessTokenCache('driver', $driver, $token);
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }

        trace(TraceEvents::LOGIN);

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

    public function phoneVerification(array $args)
    {
        try {
            $driver = $this->model->where('phone', $args['phone'])->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.driver_not_found'));
        }

        if (config('custom.send_otp')) {
            $verification_code = mt_rand(1000, 9999);;
        } else {
            $verification_code = '0000';
        }

        $driver->update(['otp' => $verification_code]);

        $message = __('lang.verification_code', [
            'verification_code' => $verification_code,
            'signature' => config('custom.otp_signature'),
        ]);

        //SendOtp::dispatch($args['phone'], $message);

        return [
            "verificationCode" => $verification_code
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
