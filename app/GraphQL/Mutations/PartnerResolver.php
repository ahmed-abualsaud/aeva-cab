<?php

namespace App\GraphQL\Mutations;

use \App\Partner;
use App\PartnerDriver;
use \App\Traits\UploadFile;
use Illuminate\Support\Arr;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class PartnerResolver
{
    use UploadFile;
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
        $input = collect($args)->except(['directive', 'logo'])->toArray();
        $input['password'] = Hash::make($input['phone1']);

        if ($args['logo']) {
            $url = $this->uploadOneFile($args['logo'], 'images');
            $input['logo'] = $url;
        }
         
        $partner = Partner::create($input);

        return $partner;
    }

    public function update($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $input = collect($args)->except(['id', 'directive', 'logo'])->toArray();

        try {
            $partner = Partner::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('The provided partner ID is not found.');
        }

        if ($args['logo']) {
            if ($partner->logo) $this->deleteOneFile($partner->logo, 'images');
            $url = $this->uploadOneFile($args['logo'], 'images');
            $input['logo'] = $url;
        }

        $partner->update($input);

        return $partner;
    }

    public function login($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
    
        $credentials = Arr::only($args, ['email', 'password']);

        if (! $token = auth('partner')->attempt($credentials)) {
        throw new CustomException(
            'Authentication Faild',
            'The provided authentication credentials are invalid.',
            'Authentication'
        );
        }

        $partner = auth('partner')->user();

        return [
        'access_token' => $token,
        'partner' => $partner
        ];

    }

    public function assignDriver($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $data = [];
        $arr = [];

        foreach($args['driver_id'] as $val) {
            $arr['partner_id'] = $args['partner_id'];
            $arr['driver_id'] = $val;

            array_push($data, $arr);
        } 

        try {
            PartnerDriver::insert($data);
        } catch (\Exception $e) {
            throw new CustomException(
              'Assignment faild.',
              'Driver can not be assigned to the same partner more than once.',
              'Integrity constraint violation.'
            );
        }
 
        return [
            "status" => true,
            "message" => "Selected drivers have been assigned successfully."
        ];
    }

    public function unassignDriver($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            PartnerDriver::where('partner_id', $args['partner_id'])
                ->whereIn('driver_id', $args['driver_id'])
                ->delete();
        } catch (\Exception $e) {
            throw new CustomException(
                'Assignment cancellation faild.',
                'Something went wrong',
                'Unknown.'
            );
        }

        return [
            "status" => true,
            "message" => "Selected drivers have been unassigned successfully."
        ];
    }
}