<?php

namespace App\GraphQL\Mutations;

use App\Driver;
use App\DriverVehicle;
use App\Traits\HandleUpload;
use App\Exceptions\CustomException;
use App\PartnerDriver;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DriverResolver 
{
    use HandleUpload;
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        $input = collect($args)->except(['directive', 'avatar'])->toArray();
        $input['password'] = Hash::make($input['phone']);
 
        if (array_key_exists('avatar', $args) && $args['avatar']) {
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }

        $driver = Driver::create($input);
        
        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            $this->createPartnerDriver($args['partner_id'], $driver->id);
        }

        return $driver;
    }

    public function update($_, array $args)
    {
        $input = collect($args)->except(['id', 'directive', 'avatar'])->toArray();

        try {
            $driver = Driver::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new CustomException(__('lang.driver_not_found'));
        }

        if (array_key_exists('avatar', $args) && $args['avatar']) {
            if ($driver->avatar) $this->deleteOneFile($driver->avatar, 'avatars');
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }

        $driver->update($input);

        return $driver;
    }

    protected function createPartnerDriver($partner_id, $driver_id)
    {
        PartnerDriver::create([
            "partner_id" => $partner_id,
            "driver_id" => $driver_id
        ]);
    }

    public function login($_, array $args)
    {
        $emailOrPhone = filter_var($args['emailOrPhone'], FILTER_VALIDATE_EMAIL);

        if ($emailOrPhone) {
            $credentials["email"] = $args['emailOrPhone'];
        } else {
            $credentials["phone"] = $args['emailOrPhone'];
        } 

        $credentials["password"] = $args['password'];

        if (!$token = auth('driver')->attempt($credentials)) {
            throw new CustomException(__('lang.invalid_auth_credentials'));
        }

        $driver = auth('driver')->user();

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

    public function updatePassword($_, array $args)
    {
        try {
            $driver = Driver::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            return [
                'status' => false, 
                'message' => __('lang.driver_not_found')
            ];
        }

        if (!(Hash::check($args['current_password'], $driver->password))) {
            return [
                'status' => false,
                'message' => __('lang.password_missmatch')
            ];
        }

        if (strcmp($args['current_password'], $args['new_password']) == 0) {
            return [
                'status' => false,
                'message' => __('lang.type_new_password')
            ];
        }

        $driver->password = Hash::make($args['new_password']);
        $driver->save();

        return [
            'status' => true,
            'message' => __('lang.password_changed')
        ];

    }

    public function assignVehicle($_, array $args)
    {
        $data = [];
        $arr = [];

        foreach($args['vehicle_id'] as $val) {
            $arr['driver_id'] = $args['driver_id'];
            $arr['vehicle_id'] = $val;
            array_push($data, $arr);
        } 

        try {
            DriverVehicle::insert($data);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.assignment_failed'));
        }
 
        return [
            "status" => true,
            "message" => __('lang.assign_vehicle')
        ];
    }

    public function unassignVehicle($_, array $args)
    {
        try {
            DriverVehicle::where('driver_id', $args['driver_id'])
                ->whereIn('vehicle_id', $args['vehicle_id'])
                ->delete();
        } catch (\Exception $e) {
            throw new CustomException(__('lang.assign_cancel_failed') . $e->getMessage());
        }

        return [
            "status" => true,
            "message" => __('lang.unassign_vehicle')
        ];
    }

    public function destroy($_, array $args)
    {
        return Driver::whereIn('id', $args['id'])->delete();
    }

}