<?php

namespace App\GraphQL\Mutations;

use App\OndemandRequest;
use App\OndemandRequestVehicle;
use App\OndemandRequestLine;
use App\DeviceToken;
use App\Jobs\PushNotification;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OndemandRequestResolver
{
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
        try {
            $input = collect($args)->except(['directive', 'vehicles', 'lines'])->toArray();
            $request = OndemandRequest::create($input);
 
            $vehicles_data = array(); 
            $vehicles_arr = array();
            foreach($args['vehicles'] as $vehicle) {
                $vehicles_arr['request_id'] = $request->id;
                $vehicles_arr['car_type_id'] = $vehicle['car_type_id'];
                $vehicles_arr['car_model_id'] = $vehicle['car_model_id'];
                $vehicles_arr['count'] = $vehicle['count'];
                array_push($vehicles_data, $vehicles_arr);
            } 
            $vehicles = OndemandRequestVehicle::insert($vehicles_data);

            $lines_data = array(); 
            $lines_arr = array();
            foreach($args['lines'] as $line) {
                $lines_arr['request_id'] = $request->id;
                $lines_arr['from_lat'] = $line['from_lat'];
                $lines_arr['from_lng'] = $line['from_lng'];
                $lines_arr['to_lat'] = $line['to_lat'];
                $lines_arr['to_lng'] = $line['to_lng'];
                $lines_arr['from_address'] = $line['from_address'];
                $lines_arr['to_address'] = $line['to_address'];
                array_push($lines_data, $lines_arr);
            } 
            $lines = OndemandRequestLine::insert($lines_data);
        } catch (\Exception $e) {
            throw new \Exception('We could not able to create this request.' . $e->getMessage());
        }
        
        return $request;
    }

    public function update($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $input = collect($args)->except(['id', 'directive'])->toArray();

        try {
            $request = OndemandRequest::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('The provided request ID is not found.');
        }

        if (array_key_exists('status', $args) && $args['status']) { 
            
            if ($args['status'] === 'CANCELLED' && $request->status !== 'PENDING') {
                throw new \Exception('This request can not be cancelled.');
            }

            if ($args['status'] !== 'CANCELLED') {
                $token = DeviceToken::where('tokenable_id', $request->user_id)
                    ->where('tokenable_type', 'App\User')
                    ->select('device_id')
                    ->pluck('device_id');
    
                $response = $args['response'] ? ' '.$args['response'] : '';
                $notificationMsg = 'Your Ondemand request ID ' . $request->id . ' has ' . strtolower($args['status']) . '.' . $response;
    
                $data = [
                    "request_id" => $request->id, 
                    "status" => $args['status']
                ];
    
                PushNotification::dispatch($token, $notificationMsg, $data);
            }
        }

        $request->update($input);

        return $request;
    }
}
