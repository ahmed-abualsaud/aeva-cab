<?php

namespace App\GraphQL\Mutations;

use App\Partner;
use App\PartnerUser;
use App\PartnerDriver;
use App\Traits\Uploadable;
use Illuminate\Support\Arr;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PartnerResolver
{
    use Uploadable;
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
            throw new \Exception('The provided partner ID is not found.');
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
                'The provided authentication credentials are invalid.',
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
              'Driver can not be assigned to the same partner more than once.',
              'customValidation'
            );
        }
 
        return [
            "status" => true,
            "message" => "Selected drivers have been assigned successfully."
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
                'Assignment cancellation faild.',
                'customValidation'
            );
        }

        return [
            "status" => true,
            "message" => "Selected drivers have been unassigned successfully."
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
              'User can not be assigned to the same partner more than once.',
              'customValidation'
            );
        }
 
        return [
            "status" => true,
            "message" => "Selected users have been assigned successfully."
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
                'Assignment cancellation faild.',
                'customValidation'
            );
        }

        return [
            "status" => true,
            "message" => "Selected users have been unassigned successfully."
        ];
    }

    public function updatePassword($_, array $args)
    {
        try {
            $partner = Partner::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('The provided partner ID is not found.');
        }

        if (!(Hash::check($args['current_password'], $partner->password))) {
            throw new CustomException(
                'Your current password does not matches with the password you provided.',
                'customValidation'
            );
        }

        if (strcmp($args['current_password'], $args['new_password']) == 0) {
            throw new CustomException(
                'New Password cannot be same as your current password. Please choose a different password.',
                'customValidation'
            );
        }

        $partner->password = Hash::make($args['new_password']);
        $partner->save();

        return 'Password changed successfully.';

    }
}