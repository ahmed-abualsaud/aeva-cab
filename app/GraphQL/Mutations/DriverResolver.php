<?php

namespace App\GraphQL\Mutations;

use App\Driver;
use App\DriverVehicle;
use App\Traits\HandleUpload;
use Illuminate\Support\Arr;
use App\Exceptions\CustomException;
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

        return $driver;
    }

    public function update($_, array $args)
    {
        $input = collect($args)->except(['id', 'directive', 'avatar'])->toArray();

        try {
            $driver = Driver::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new CustomException('The provided driver ID is not found.');
        }

        if (array_key_exists('avatar', $args) && $args['avatar']) {
            if ($driver->avatar) $this->deleteOneFile($driver->avatar, 'avatars');
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }

        $driver->update($input);

        return $driver;
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

        if (! $token = auth('driver')->attempt($credentials)) {
            throw new CustomException('The provided authentication credentials are invalid.');
        }

        $driver = auth('driver')->user();

        if (array_key_exists('device_id', $args) 
            && $args['device_id'] 
            && $driver->device_id != $args['device_id']) 
        {
            $driver->update(['device_id' => $args['device_id']]);
        }

        // if (array_key_exists('device_id', $args) && array_key_exists('platform', $args)) {
        //     try {
        //         DeviceToken::where('device_id', $args['device_id'])
        //             ->where('tokenable_type', 'App\Driver')
        //             ->firstOrFail();
        //     } catch (ModelNotFoundException $e) {
        //         $tokenInput = collect($args)->only(['platform', 'device_id'])->toArray();
        //         $tokenInput['tokenable_id'] = $driver->id;
        //         $tokenInput['tokenable_type'] = 'App\Driver';
        //         DeviceToken::create($tokenInput);
        //     }
        // }

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
                'message' => 'The provided driver ID is not found.'
            ];
        }

        if (!(Hash::check($args['current_password'], $driver->password))) {
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

        $driver->password = Hash::make($args['new_password']);
        $driver->save();

        return [
            'status' => true,
            'message' => 'Password changed successfully.'
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
            throw new CustomException('Assignment faild.' . $e->getMessage());
        }
 
        return [
            "status" => true,
            "message" => "Selected vehicles have been assigned successfully."
        ];
    }

    public function unassignVehicle($_, array $args)
    {
        try {
            DriverVehicle::where('driver_id', $args['driver_id'])
                ->whereIn('vehicle_id', $args['vehicle_id'])
                ->delete();
        } catch (\Exception $e) {
            throw new CustomException('Assignment cancellation faild.' . $e->getMessage());
        }

        return [
            "status" => true,
            "message" => "Selected vehicles have been unassigned successfully."
        ];
    }

    public function destroy($_, array $args)
    {
        return Driver::whereIn('id', $args['id'])->forceDelete();
    }

}