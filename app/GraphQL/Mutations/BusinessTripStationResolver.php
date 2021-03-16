<?php

namespace App\GraphQL\Mutations;

use Carbon\Carbon;
use App\BusinessTrip;
use App\SchoolRequest;
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
    public function create($_, array $args)
    { 
        try {
            $arr = [
                'trip_id' => $args['trip_id'],
                'created_at' => now(), 
                'updated_at' => now()
            ];
            foreach($args['stations'] as $station) {
                $arr['name'] = $station['name'];
                $arr['latitude'] = $station['latitude'];
                $arr['longitude'] = $station['longitude'];
                $arr['state'] = $station['state'];
                $arr['accepted_at'] = $station['accepted_at'];
                $data[] = $arr;
            } 
            BusinessTripStation::insert($data);
        } catch (\Exception $e) {
            throw new CustomException('We could not able to insert these stations.' . $e->getMessage());
        }

        return BusinessTripStation::where('trip_id', $args['trip_id'])
            ->get();
    }

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

    public function updateRoute($_, array $args)
    {
        try {
            
            $cases = []; $ids = []; $distance = []; $duration = [];

            foreach ($args['stations'] as $value) {
                $id = (int) $value['id'];
                $cases[] = "WHEN {$id} then ?";
                $distance[] = $value['distance'];
                $duration[] = $value['duration'];
                $ids[] = $id;
            }

            $ids = implode(',', $ids);
            $cases = implode(' ', $cases);
            $params = array_merge($distance, $duration);
            $params[] = Carbon::now();

            DB::update("UPDATE `business_trip_stations` SET 
                `distance` = CASE `id` {$cases} END, 
                `duration` = CASE `id` {$cases} END, 
                `updated_at` = ? 
                WHERE `id` in ({$ids})", $params);

            $total = end($args['stations']);

            BusinessTrip::find($args['trip_id'])
                ->update(['distance' => $total['distance'], 'duration' => $total['duration']]);

            return true;
            
        } catch (\Exception $e) {
            throw new CustomException('Could not able to update.');
        }
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

    public function assignUsers($_, array $args)
    {
        try {
            $arr = [
                'trip_id' => $args['trip_id'],
                'station_id' => $args['station_id'],
                'subscription_verified_at' => now(),
                'created_at' => now(), 'updated_at' => now()
            ];
            foreach($args['users'] as $user) {
                $arr['user_id'] = $user;
                $data[] = $arr;
            }
            return BusinessTripUser::upsert($data, ['station_id', 'updated_at']);
        } catch (\Exception $e) {
            throw new CustomException('We could not able to assign selected users to specified station.');
        }
    }

    public function unassignUsers($_, array $args)
    {
        try {
            $users = BusinessTripUser::where('station_id', $args['station_id'])
                ->whereIn('user_id', $args['users']);

            $schoolRequests = $users->get()
                ->where('creator_type', 'App\\SchoolRequest')
                ->pluck('creator_id')
                ->toArray();
                
            if ($schoolRequests) 
                SchoolRequest::restore($schoolRequests);

            return $users->update(['station_id' => null]);

        } catch (\Exception $e) {
            throw new CustomException('We could not able to unassign selected users from specified station.');
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
                'user_id' => $station['creator_id'],
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

        if ($station->creator_type === 'App\SchoolRequest')
            SchoolRequest::restore($station->creator_id);
        
        $station->delete();

        return $station;
    }
}
