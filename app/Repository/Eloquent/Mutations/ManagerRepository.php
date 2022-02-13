<?php

namespace App\Repository\Eloquent\Mutations;

use App\Manager;
use App\Traits\HandleAccessTokenCache;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repository\Eloquent\BaseRepository;
use App\Repository\Mutations\ManagerRepositoryInterface;

class ManagerRepository extends BaseRepository implements ManagerRepositoryInterface
{
    use HandleAccessTokenCache;

    public function __construct(Manager $model)
    {
        parent::__construct($model);
    }

    public function create(array $args)
    {
        $input = collect($args)->except(['directive'])->toArray();
        $input['password'] = Hash::make($input['phone']);

        $manager = $this->model->create($input);

        return $manager;
    }

    public function update(array $args)
    {
        $input = collect($args)->except(['id', 'directive'])->toArray();

        try {
            $manager = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.manager_not_found'));
        }
        
        $manager->update($input);

        return $manager;
    }

    public function login(array $args)
    {
        $credentials = [
            "phone" => $args['emailOrPhone'],
            "password" => $args['password']
        ];

        if (! $token = auth('manager')->attempt($credentials)) {
            throw new CustomException(
                __('lang.invalid_auth_credentials'),
                'customValidation'
            ); 
        }

        $manager = auth('manager')->user();

        $this->handleAccessTokenCache('manager', $manager, $token);

        return [
            'access_token' => $token,
            'manager' => $manager
        ];

    }

    public function updatePassword(array $args)
    {
        try {
            $manager = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.manager_not_found'));
        }

        if (!(Hash::check($args['current_password'], $manager->password))) {
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

        $manager->password = Hash::make($args['new_password']);

        $manager->save();

        auth('manager')->onceUsingId($manager->id);

        $token = auth('manager')->fromUser($manager);
        
        $this->handleAccessTokenCache('manager', $manager, $token);
        
        return [
            'access_token' => $token,
            'manager' => $manager
        ];

    }

    public function destroy(array $args) {
        try {
            $manager = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.manager_not_found'));
        }

        $manager->delete();

        return $manager;
    }
}
