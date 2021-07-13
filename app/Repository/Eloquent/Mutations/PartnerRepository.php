<?php

namespace App\Repository\Eloquent\Mutations;

use App\Partner;
use App\PartnerUser;
use App\PartnerDriver;
use App\Traits\HandleUpload;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repository\Eloquent\BaseRepository;
use App\Repository\Mutations\PartnerRepositoryInterface;

class PartnerRepository extends BaseRepository implements PartnerRepositoryInterface
{
    use HandleUpload;

    public function __construct(Partner $model)
    {
        parent::__construct($model);
    }

    public function create(array $args)
    {
        $input = collect($args)->except(['directive', 'logo'])->toArray();
        $input['password'] = Hash::make($input['phone1']);

        if (array_key_exists('logo', $args) && $args['logo']) {
            $url = $this->uploadOneFile($args['logo'], 'images');
            $input['logo'] = $url;
        }
         
        $partner = $this->model->create($input);

        return $partner;
    }

    public function update(array $args)
    {
        $input = collect($args)->except(['id', 'directive', 'logo'])->toArray();

        try {
            $partner = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.partner_not_found'));
        }

        if (array_key_exists('logo', $args) && $args['logo']) { 
            if ($partner->logo) $this->deleteOneFile($partner->logo, 'images');
            $url = $this->uploadOneFile($args['logo'], 'images');
            $input['logo'] = $url;
        }

        $partner->update($input);

        return $partner;
    }

    public function login(array $args)
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
                __('lang.invalid_auth_credentials'),
                'customValidation'
            ); 
        }

        $partner = auth('partner')->user();

        return [
        'access_token' => $token,
        'partner' => $partner
        ];

    }

    public function assignDriver(array $args)
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
              __('lang.driver_assign_failed'),
              'customValidation'
            );
        }
 
        return [
            "status" => true,
            "message" => __('lang.driver_assigned')
        ];
    }

    public function unassignDriver(array $args)
    {
        try {
            PartnerDriver::where('partner_id', $args['partner_id'])
                ->whereIn('driver_id', $args['driver_id'])
                ->delete();
        } catch (\Exception $e) {
            throw new CustomException(
                __('lang.assign_cancel_failed'),
                'customValidation'
            );
        }

        return [
            "status" => true,
            "message" => __('lang.driver_unassigned')
        ];
    }

    public function assignUser(array $args)
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
              __('lang.user_assign_failed'),
              'customValidation'
            );
        }
 
        return [
            "status" => true,
            "message" => __('lang.user_assigned')
        ];
    }

    public function unassignUser(array $args)
    {
        try {
            PartnerUser::where('partner_id', $args['partner_id'])
                ->whereIn('user_id', $args['user_id'])
                ->delete();
        } catch (\Exception $e) {
            throw new CustomException(
                __('lang.assign_cancel_failed'),
                'customValidation'
            );
        }

        return [
            "status" => true,
            "message" => __('lang.user_unassigned')
        ];
    }

    public function updatePassword(array $args)
    {
        try {
            $partner = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.partner_not_found'));
        }

        if (!(Hash::check($args['current_password'], $partner->password))) {
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

        $partner->password = Hash::make($args['new_password']);
        $partner->save();

        return __('lang.password_changed');

    }
}
