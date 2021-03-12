<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\PartnerUser;
use App\BusinessTrip;
use App\Jobs\SendOtp;
use App\SchoolRequest;
use App\BusinessTripUser;
use Illuminate\Support\Arr;
use App\BusinessTripStation;
use App\BusinessTripSchedule;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BusinessTripResolver
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

            if (array_key_exists('request_ids', $args) && $args['request_ids']) {
                $this->createStationsAndDestinations($args['users'], $args['schools'], $businessTrip->id);
                $this->assignUsersToStationsAndDestinations($args['users'], $businessTrip->id);
                $this->createScheduleForEachUser($args['users'], $businessTrip->id);
                SchoolRequest::accept($args['request_ids']);
            }

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException('We could not able to create this trip!');
        }

        return $businessTrip;
    }

    public function copy($_, array $args)
    {
        DB::beginTransaction();
        try {
            $businessTrip = $this->createTripCopy($args);

            if ($args['include_stations'])
                $this->createStationsCopy($args['id'], $businessTrip->id);

            if ($args['include_subscriptions'])
                $this->createSubscriptionsCopy($args['id'], $businessTrip->id);

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException('We could not able to copy this trip!');
        }

        return $businessTrip;
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

            $this->createScheduleForEachUser($args['users'], $args['trip_id']);
            SchoolRequest::accept($args['request_ids']);

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException('We could not able to add these requests to a business trip!');
        }
    }

    public function update($_, array $args)
    {
        try {
            $tripInput = Arr::except($args, ['directive']);
            $trip = BusinessTrip::findOrFail($args['id']);
            $trip->update($tripInput);
        } catch (ModelNotFoundException $e) {
            throw new CustomException('Trip with the provided ID is not found.');
        }
    
        return $trip;
    }

    public function inviteUser($_, array $args)
    {
        try {
            $arr = [
                'trip_id' => $args['trip_id'],
                'created_at' => now(), 
                'updated_at' => now()
            ];

            foreach($args['user_id'] as $val) {
                $arr['user_id'] = $val;
                $data[] = $arr;
            } 

            BusinessTripUser::insert($data);
        } catch (\Exception $e) {
            throw new CustomException('We could not able to invite selected users!');
        }

        $this->notifyUserViaSms($args);

        return 'Selected users have been invited but still not verified';
    }

    public function subscribeUser($_, array $args)
    {
        try {
            $arr = [
                'trip_id' => $args['trip_id'],
                'station_id' => $args['station_id'],
                'destination_id' => $args['destination_id'],
                'created_at' => now(), 
                'updated_at' => now(),
                'subscription_verified_at' => now()
            ];

            foreach($args['user_id'] as $val) {
                $arr['user_id'] = $val;
                $data[] = $arr;
            } 

            BusinessTripUser::upsert($data, ['station_id', 'destination_id', 'updated_at']);
        } catch (\Exception $e) {
            throw new CustomException('We could not able to subscribe selected users!');
        }

        return 'Selected users have been subscribed';
    }

    public function confirmUserSubscription($_, array $args) 
    {
        try {
            $trip_id = Hashids::decode($args['subscription_code']);
            $trip = BusinessTrip::findOrFail($trip_id[0]);
        } catch (\Exception $e) {
            throw new CustomException('Subscription code is not valid.');
        }
        
        try {
            $tripUser = BusinessTripUser::where('trip_id', $trip->id)
                ->where('user_id', $args['user_id'])
                ->firstOrFail();
            if ($tripUser->subscription_verified_at) {
                throw new CustomException('You have already subscribed to this trip.');
            } else {
                $tripUser->update(['subscription_verified_at' => now()]);
            }
        } catch (ModelNotFoundException $e) {
            BusinessTripUser::create([
                'trip_id' => $trip->id,
                'user_id' => $args['user_id'],
                'subscription_verified_at' => now()
            ]);

            PartnerUser::firstOrCreate([
                'partner_id' => $trip->partner_id, 
                'user_id' => $args['user_id']
            ]);
        }
        
        return $trip;
    }

    public function unsubscribeUser($_, array $args)
    {
        try {
            $users = BusinessTripUser::where('trip_id', $args['trip_id'])
                ->whereIn('user_id', $args['user_id']);

            if ($users->count()) {
                $schoolRequests = $users->get()
                    ->where('creator_type', 'App\\SchoolRequest')
                    ->pluck('creator_id')
                    ->toArray();
                    
                if ($schoolRequests) SchoolRequest::restore($schoolRequests);

                $users->delete();
            }
        } catch (\Exception $e) {
            throw new CustomException('Subscription cancellation failed.');
        }
        
        return [
            "status" => true,
            "message" => "Subscription cancellation has done successfully."
        ];
    }

    public function verifyUserSubscription($_, array $args)
    {
        try {
            BusinessTripUser::where('trip_id', $args['trip_id'])
                ->where('user_id', $args['user_id'])
                ->update(['subscription_verified_at' => $args['subscription_verified_at']]);
        } catch (\Exception $e) {
            throw new CustomException('We could not able to toggle this subscription!');
        }

        return "Subscription toggled successfully";
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

    protected function notifyUserViaSms(array $args)
    {
        try {
            $phones = User::select('phone')
                ->whereIn('id', $args['user_id'])
                ->pluck('phone')
                ->toArray();

            $message = 'Dear valued user, kindly use this code to confirm your subscription for '
            . $args['trip_name'] .' trip: ' 
            . $args['subscription_code'];
            
            SendOtp::dispatch(implode(",", $phones), $message); 
        } catch (\Exception $e) {
            //
        }
    }

    protected function createBusinessTrip($input)
    {
        $businessTrip = BusinessTrip::create($input);
        $businessTrip->update(['subscription_code' => Hashids::encode($businessTrip->id)]);

        return $businessTrip;
    }

    protected function createTripCopy(array $args)
    {
        $originalTrip = BusinessTrip::select(
            'partner_id', 'driver_id', 'vehicle_id', 'start_date', 'end_date', 
            'return_time', 'days', 'duration', 'distance', 'group_chat'
            )
            ->findOrFail($args['id'])
            ->toArray();

        $originalTrip['name'] = $args['name'];
        
        return $this->createBusinessTrip($originalTrip);
    }

    protected function createStationsCopy($oldTripId, $newTripId)
    {
        $originalStations = BusinessTripStation::select(
            'name', 'latitude', 'longitude', 'duration', 'distance', 'state'
            )
            ->where('trip_id', $oldTripId)
            ->get();

        foreach($originalStations as $station) {
            $station->trip_id = $newTripId;
            $station->created_at = now();
            $station->updated_at = now();
            $station->accepted_at = now();
        }

        return BusinessTripStation::insert($originalStations->toArray());
    }

    protected function createSubscriptionsCopy($oldTripId, $newTripId)
    {
        $originalSubscriptions = BusinessTripUser::select('user_id')
            ->where('trip_id', $oldTripId)
            ->get();

        foreach($originalSubscriptions as $ubscription) {
            $ubscription->trip_id = $newTripId;
            $ubscription->created_at = now();
            $ubscription->updated_at = now();
            $ubscription->subscription_verified_at = now();
        }

        return BusinessTripUser::insert($originalSubscriptions->toArray());
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
}
