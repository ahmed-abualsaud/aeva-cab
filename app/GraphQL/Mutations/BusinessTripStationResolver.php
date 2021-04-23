<?php

namespace App\GraphQL\Mutations;

use App\BusinessTripUser;
use App\BusinessTripStation;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BusinessTripStationResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function update($_, array $args)
    {
        $input = collect($args)->except(['id', 'directive', 'trip_id', 'state'])->toArray();

        try {
            $station = BusinessTripStation::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new CustomException('The provided station ID is not found.');
        }

        if (array_key_exists('state', $args) && $args['state'] && $args['state'] != $station->state) {
            $input['state'] = $args['state'];
            $updatedStation = BusinessTripStation::where('state', $args['state'])
                ->where('trip_id', $args['trip_id'])
                ->where('id', '<>', $station->id)
                ->first();
            if ($updatedStation) $updatedStation->update(['state' => $station->state]);
        }

        $station->update($input);

        return $station;
    }

    public function assignUser($_, array $args)
    {
        try {
            $data = [
                'trip_id' => $args['trip_id'],
                'user_id' => $args['user_id'],
                'station_id' => $args['station_id'],
                'destination_id' => $args['destination_id'],
                'subscription_verified_at' => now(),
                'created_at' => now(), 'updated_at' => now()
            ];
            return BusinessTripUser::upsert($data, ['station_id', 'destination_id', 'updated_at']);
        } catch (\Exception $e) {
            throw new CustomException('Something went wrong! please try again');
        }
    }

    public function acceptStation($_, array $args)
    {
        DB::beginTransaction();
        try {
            $station = BusinessTripStation::where('id', $args['station_id'])->firstOrFail();
            $station->update([
                'name' => $args['station_name'],
                'state' => 'PICKABLE', 
                'accepted_at' => now()
            ]);
            
            $data =[
                'trip_id' => $args['trip_id'],
                'station_id' => $args['station_id'],
                'user_id' => $station['request_id'],
                'subscription_verified_at' => now(),
                'created_at' => now(), 
                'updated_at' => now()
            ];
            BusinessTripUser::upsert($data, ['station_id', 'updated_at']);

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException('We could not able to accept this station!');
        }

        return $station;
    }

    public function destroy($_, array $args)
    {
        try {
            $station = BusinessTripStation::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new CustomException('Station with the provided ID is not found.');
        }

        /*
        * Revert Business Request

        if ($station->request_type)
            $station->request_type::restore($station->request_id);
        */
        
        $station->delete();

        return $station;
    }
}
