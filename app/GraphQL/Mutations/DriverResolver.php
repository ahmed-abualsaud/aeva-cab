<?php

namespace App\GraphQL\Mutations;

use App\Driver;
use App\DeviceToken;
use App\DriverVehicle;
use App\Traits\UploadOneFile;
use App\Traits\DeleteOneFile;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Facades\Hash;

class DriverResolver 
{
    use UploadOneFile;
    use DeleteOneFile;
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function create($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
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

    public function update($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $input = collect($args)->except(['id', 'directive', 'avatar'])->toArray();

        try {
            $driver = Driver::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('The provided driver ID is not found.');
        }

        if (array_key_exists('avatar', $args) && $args['avatar']) {
            if ($driver->avatar) $this->deleteOneFile($driver->avatar, 'avatars');
            $url = $this->uploadOneFile($args['avatar'], 'avatars');
            $input['avatar'] = $url;
        }

        $driver->update($input);

        return $driver;
    }

    public function login($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
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
            throw new \Exception('The provided authentication credentials are invalid.');
        }

        $driver = auth('driver')->user();

        if (array_key_exists('device_id', $args) && array_key_exists('platform', $args)) {
            try {
                DeviceToken::where('device_id', $args['device_id'])->firstOrFail();
            } catch (ModelNotFoundException $e) {
                $tokenInput = collect($args)->only(['platform', 'device_id'])->toArray();
                $tokenInput['tokenable_id'] = $driver->id;
                $tokenInput['tokenable_type'] = 'App\Driver';
                DeviceToken::create($tokenInput);
            }
        }

        return [
            'access_token' => $token,
            'driver' => $driver
        ];

    }

    public function assignVehicle($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
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
            throw new \Exception('Assignment faild.' . $e->getMessage());
        }
 
        return [
            "status" => true,
            "message" => "Selected vehicles have been assigned successfully."
        ];
    }

    public function unassignVehicle($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            DriverVehicle::where('driver_id', $args['driver_id'])
                ->whereIn('vehicle_id', $args['vehicle_id'])
                ->delete();
        } catch (\Exception $e) {
            throw new \Exception('Assignment cancellation faild.' . $e->getMessage());
        }

        return [
            "status" => true,
            "message" => "Selected vehicles have been unassigned successfully."
        ];
    }
}