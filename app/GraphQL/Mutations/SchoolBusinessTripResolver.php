<?php

namespace App\GraphQL\Mutations;

use App\BusinessTrip;
use App\SchoolRequest;
use App\BusinessTripUser;
use Illuminate\Support\Arr;
use App\BusinessTripStation;
use App\BusinessTripSchedule;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Vinkla\Hashids\Facades\Hashids;

class SchoolBusinessTripResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        DB::beginTransaction();
        try {
            $input = Arr::except($args, ['directive', 'request_ids', 'schools', 'users']);
            $businessTrip = $this->createBusinessTrip($input);
            $this->createStationsAndDestinations($args['users'], $args['schools'], $businessTrip->id);
            $this->assignUsersToStationsAndDestinations($args['users'], $businessTrip->id);
            $this->createScheduleForEachUser($args['users'], $businessTrip->id);
            SchoolRequest::accept($args['request_ids']);

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException('We could not able to create this trip!');
        }
    }

    public function addSchoolRequest($_, array $args)
    {
        DB::beginTransaction();
        try {
            if (array_key_exists('station_id', $args) && array_key_exists('destination_id', $args)) {
                $this->assignUsersToStationAndDestination($args);
            } else if (array_key_exists('station_id', $args)) {
                $this->createDestinations($args['schools'], $args['trip_id']);
                $this->assignUsersToDestinations($args['users'], $args['trip_id']);
                $this->assignUsersToStation($args);
            } else if (array_key_exists('destination_id', $args)) {
                $this->createStations($args['users'], $args['trip_id']);
                $this->assignUsersToStations($args['users'], $args['trip_id']);
                $this->assignUsersToDestination($args);
            } else {
                $this->createStationsAndDestinations($args['users'], $args['schools'], $args['trip_id']);
                $this->assignUsersToStationsAndDestinations($args['users'], $args['trip_id']);
            }

            $this->updateTripSchedule($args);
            $this->createScheduleForEachUser($args['users'], $args['trip_id']);
            SchoolRequest::accept($args['request_ids']);

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException('We could not able to add these requests to a business trip!');
        }
    }

    protected function assignUsersToStationAndDestination(array $args)
    {
        try {
            
            $data = $this->stationsData($args);

            BusinessTripUser::upsert(
                $data, ['station_id', 'destination_id', 'creator_type', 'creator_id']
            );

        } catch(\Exception $e) {
            throw new CustomException('We could not able to assign users to specified station');
        }
    }

    protected function assignUsersToStation(array $args)
    {
        try {
            
            $data = $this->stationsData($args);

            BusinessTripUser::upsert(
                $data, ['station_id', 'creator_type', 'creator_id']
            );

        } catch(\Exception $e) {
            throw new CustomException('We could not able to assign users to specified station');
        }
    }

    protected function assignUsersToDestination(array $args)
    {
        try {
            
            $data = $this->stationsData($args);

            BusinessTripUser::upsert(
                $data, ['destination_id', 'creator_type', 'creator_id']
            );

        } catch(\Exception $e) {
            throw new CustomException('We could not able to assign users to specified station');
        }
    }

    protected function assignUsersToStationsAndDestinations($users, $trip_id)
    {
        $arr = $this->subscriptionData($trip_id);

        $stations = $this->stationsByTrip($trip_id);

        foreach($users as $user) {
            $arr['user_id'] = $user['id'];
            $arr['station_id'] = $stations->firstWhere('creator_id', $user['request_id'])->id;
            $arr['destination_id'] = $stations->firstWhere('name', $user['school'])->id;
            $arr['creator_id'] = $user['request_id'];
            $data[] = $arr;
        }

        BusinessTripUser::insert($data);
    }

    protected function assignUsersToStations($users, $trip_id)
    {
        $arr = $this->subscriptionData($trip_id);

        $stations = $this->stationsByTrip($trip_id);

        foreach($users as $user) {
            $arr['user_id'] = $user['id'];
            $arr['station_id'] = $stations->firstWhere('creator_id', $user['request_id'])->id;
            $arr['creator_id'] = $user['request_id'];
            $data[] = $arr;
        }

        BusinessTripUser::insert($data);
    }

    protected function assignUsersToDestinations($users, $trip_id)
    {
        $arr = $this->subscriptionData($trip_id);

        $stations = $this->stationsByTrip($trip_id);

        foreach($users as $user) {
            $arr['user_id'] = $user['id'];
            $arr['destination_id'] = $stations->firstWhere('name', $user['school'])->id;
            $arr['creator_id'] = $user['request_id'];
            $data[] = $arr;
        }

        BusinessTripUser::insert($data);
    }

    protected function createStationsAndDestinations($users, $schools, $trip_id)
    {
        $usersData = $this->usersData($users, $trip_id);
        $schoolsData = $this->schoolsData($schools, $trip_id);
        
        BusinessTripStation::insert(array_merge($usersData, $schoolsData));
    }

    protected function createStations($users, $trip_id)
    {
        $usersData = $this->usersData($users, $trip_id);
        
        BusinessTripStation::insert($usersData);
    }

    protected function createDestinations($schools, $trip_id)
    {
        $schoolsData = $this->schoolsData($schools, $trip_id);
        
        BusinessTripStation::insert($schoolsData);
    }
    

    protected function createScheduleForEachUser($users, $trip_id)
    {
        $tripScheduleArr = [
            'trip_id' => $trip_id
        ];

        foreach($users as $user) {
            $tripScheduleArr['user_id'] = $user['id'];
            $tripScheduleArr['days'] = json_encode($user['days']);
            $tripScheduleData[] = $tripScheduleArr;
        }

        BusinessTripSchedule::upsert($tripScheduleData, ['days']);
    }

    protected function createBusinessTrip($input)
    {
        $businessTrip = BusinessTrip::create($input);
        $businessTrip->update(['subscription_code' => Hashids::encode($businessTrip->id)]);

        return $businessTrip;
    }

    protected function usersData(array $users, Int $trip_id)
    {
        $pickable = [
            'state' => 'PICKABLE',
            'creator_type' => 'App\\SchoolRequest',
            'trip_id' => $trip_id,
            'created_at' => now(), 'updated_at' => now(), 'accepted_at' => now(),
        ];
        foreach($users as $user) {
            $pickable['creator_id'] = $user['request_id'];
            $pickable['name'] = $user['address'];
            $pickable['latitude'] = $user['lat'];
            $pickable['longitude'] = $user['lng'];
            $data[] = $pickable;
        }

        return $data;
    }

    protected function schoolsData(array $schools, Int $trip_id)
    {
        $destination = [
            'creator_type' => null,
            'creator_id' => null,
            'state' => 'DESTINATION',
            'trip_id' => $trip_id,
            'created_at' => now(), 'updated_at' => now(), 'accepted_at' => now(),
        ];
        foreach($schools as $school) {
            $destination['name'] = $school['name'];
            $destination['latitude'] = $school['lat'];
            $destination['longitude'] = $school['lng'];
            $data[] = $destination;
        } 

        return $data;
    }

    protected function stationsData(array $args)
    {
        $arr = [
            'creator_type' => 'App\\SchoolRequest',
            'trip_id' => $args['trip_id'],
            'subscription_verified_at' => now(),
            'created_at' => now(), 'updated_at' => now()
        ];

        if (array_key_exists('station_id', $args)) {
            $arr['station_id'] = $args['station_id'];
        }

        if (array_key_exists('destination_id', $args)) {
            $arr['destination_id'] = $args['destination_id'];
        }
        
        foreach($args['users'] as $user) {
            $arr['user_id'] = $user['id'];
            $arr['creator_id'] = $user['request_id'];
            $data[] = $arr;
        } 

        return $data;
    }

    protected function subscriptionData(Int $trip_id)
    {
        return [
            'creator_type' => 'App\\SchoolRequest',
            'trip_id' => $trip_id,
            'subscription_verified_at' => now(),
            'created_at' => now(), 'updated_at' => now()
        ];
    }

    protected function stationsByTrip(Int $trip_id)
    {
        return BusinessTripStation::select('id', 'creator_id', 'name')
            ->where('trip_id', $trip_id)
            ->get();
    }

    protected function updateTripSchedule(array $args)
    {
        $schedule = BusinessTrip::select('days')
            ->findOrFail($args['trip_id']);

        BusinessTrip::where('id', $args['trip_id'])
            ->update(['days' => array_merge($schedule->days, $args['days'])]);
    }
}
