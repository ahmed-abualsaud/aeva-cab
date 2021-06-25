<?php

namespace App\GraphQL\Mutations;

use App\Partner;
use App\PartnerUser;
use App\PartnerDriver;
use App\Traits\HandleUpload;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PartnerResolver
{
    use HandleUpload;
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        $input = collect($args)->except(['directive', 'logo'])->toArray();
        $input['password'] = Hash::make($input['phone1']);

        if (array_key_exists('logo', $args) && $args['logo']) {
            $url = $this->uploadOneFile($args['logo'], 'images');
            $input['logo'] = $url;
        }
         
        $partner = Partner::create($input);

        return $partner;
    }

    public function update($_, array $args)
    {
        $input = collect($args)->except(['id', 'directive', 'logo'])->toArray();

        try {
            $partner = Partner::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.PartnerNotFound'));
        }

        if (array_key_exists('logo', $args) && $args['logo']) { 
            if ($partner->logo) $this->deleteOneFile($partner->logo, 'images');
            $url = $this->uploadOneFile($args['logo'], 'images');
            $input['logo'] = $url;
        }

        $partner->update($input);

        return $partner;
    }

    public function login($_, array $args)
    {
        $emailOrPhone = filter_var($args['emailOrPhone'], FILTER_VALIDATE_EMAIL);
        $credentials = [];

        if ($emailOrPhone) {
            $credentials["email"] = $args['emailOrPhone'];
        } else {
            $credentials["phone1"] = $args['emailOrPhone'];
        } 

        $credentials["password"] = $args['password'];

        if (! $token = auth('partner')->attempt($credentials)) {
            throw new CustomException(
                __('lang.InvalidAuthCredentials'),
                'customValidation'
            ); 
        }

        $partner = auth('partner')->user();

        return [
        'access_token' => $token,
        'partner' => $partner
        ];

    }

    public function assignDriver($_, array $args)
    {
        $data = []; $arr = [];
        foreach($args['driver_id'] as $val) {
            $arr['partner_id'] = $args['partner_id'];
            $arr['driver_id'] = $val;

            array_push($data, $arr);
        } 

        try {
            PartnerDriver::insert($data);
        } catch (\Exception $e) {
            throw new CustomException(
              __('lang.DriverAssignFailed'),
              'customValidation'
            );
        }
 
        return [
            "status" => true,
            "message" => __('lang.DriverAssigned')
        ];
    }

    public function unassignDriver($_, array $args)
    {
        try {
            PartnerDriver::where('partner_id', $args['partner_id'])
                ->whereIn('driver_id', $args['driver_id'])
                ->delete();
        } catch (\Exception $e) {
            throw new CustomException(
                __('lang.AssignCancelFailed'),
                'customValidation'
            );
        }

        return [
            "status" => true,
            "message" => __('lang.DriverUnassigned')
        ];
    }

    public function assignUser($_, array $args)
    {
        $data = []; $arr = [];
        foreach($args['user_id'] as $val) {
            $arr['partner_id'] = $args['partner_id'];
            $arr['user_id'] = $val;

            array_push($data, $arr);
        } 

        try {
            PartnerUser::insert($data);
        } catch (\Exception $e) {
            throw new CustomException(
              __('lang.UserAssignFailed'),
              'customValidation'
            );
        }
 
        return [
            "status" => true,
            "message" => __('lang.UserAssigned')
        ];
    }

    public function unassignUser($_, array $args)
    {
        try {
            PartnerUser::where('partner_id', $args['partner_id'])
                ->whereIn('user_id', $args['user_id'])
                ->delete();
        } catch (\Exception $e) {
            throw new CustomException(
                __('lang.AssignCancelFailed'),
                'customValidation'
            );
        }

        return [
            "status" => true,
            "message" => __('lang.UserUnassigned')
        ];
    }

    public function updatePassword($_, array $args)
    {
        try {
            $partner = Partner::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.PartnerNotFound'));
        }

        if (!(Hash::check($args['current_password'], $partner->password))) {
            throw new CustomException(
                __('lang.PasswordMissmatch'),
                'customValidation'
            );
        }

        if (strcmp($args['current_password'], $args['new_password']) == 0) {
            throw new CustomException(
                __('lang.TypeNewPassword'),
                'customValidation'
            );
        }

        $partner->password = Hash::make($args['new_password']);
        $partner->save();

        return __('lang.PasswordChanged');

    }
}