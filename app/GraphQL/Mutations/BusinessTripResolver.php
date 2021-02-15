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
            $tripInput = Arr::except($args, ['directive', 'request_ids', 'schools', 'users']);
            $businessTrip = BusinessTrip::create($tripInput);
            $businessTrip->update(['subscription_code' => Hashids::encode($businessTrip->id)]);

            if (array_key_exists('request_ids', $args) && $args['request_ids']) {
                $this->createStations($args['users'], $args['schools'], $businessTrip->id);
                $this->assignUsersToStations($args['users'], $businessTrip->id);
                $this->createScheduleForEachUser($args['users'], $businessTrip->id);
                SchoolRequest::accept($args['request_ids']);
            }

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException($e->getMessage());
        }

        return $businessTrip;
    }

    public function addSchoolRequest($_, array $args)
    {
        DB::beginTransaction();
        try {
            if (array_key_exists('station_id', $args) && $args['station_id']) {
                $this->assignUsersToStation($args);
            } else {
                $this->createStations($args['users'], $args['schools'], $args['trip_id']);
                $this->assignUsersToStations($args['users'], $args['trip_id']);
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
            $invite = array_key_exists('trip_name', $args) 
                && array_key_exists('subscription_code', $args);
            $arr = [
                'trip_id' => $args['trip_id'],
                'created_at' => now(), 
                'updated_at' => now(),
                'subscription_verified_at' => $invite ? null : now()
            ];
            foreach($args['user_id'] as $val) {
                $arr['user_id'] = $val;
                $data[] = $arr;
            } 

            BusinessTripUser::insert($data);
        } catch (\Exception $e) {
            throw new CustomException('Each user is allowed to subscribe for a trip once.');
        }

        if ($invite) $this->notifyUserViaSms($args);

        return 'Selected users have been subscribed but still not verified';
    }

    public function subscribeUser($_, array $args) 
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

    protected function assignUsersToStation(array $args)
    {
        try {
            $arr = [
                'creator_type' => 'App\\SchoolRequest',
                'trip_id' => $args['trip_id'],
                'station_id' => $args['station_id'],
                'subscription_verified_at' => now(),
                'created_at' => now(), 'updated_at' => now()
            ];
            foreach($args['users'] as $user) {
                $arr['user_id'] = $user['id'];
                $arr['creator_id'] = $user['request_id'];
                $data[] = $arr;
            } 

            BusinessTripUser::upsert($data, ['station_id', 'creator_type', 'creator_id']);

        } catch(\Exception $e) {
            throw new CustomException('We could not able to assign users to specified station');
        }
    }

    protected function assignUsersToStations($users, $trip_id)
    {
        $arr = [
            'creator_type' => 'App\\SchoolRequest',
            'trip_id' => $trip_id,
            'subscription_verified_at' => now(),
            'created_at' => now(), 'updated_at' => now()
        ];

        $stations = BusinessTripStation::select('id', 'creator_id', 'name')
            ->where('trip_id', $trip_id)
            ->get();

        foreach($users as $user) {
            $arr['user_id'] = $user['id'];
            $arr['station_id'] = $stations->firstWhere('creator_id', $user['request_id'])->id;
            $arr['destination_id'] = $stations->firstWhere('name', $user['school'])->id;
            $arr['creator_id'] = $user['request_id'];
            $data[] = $arr;
        }
        BusinessTripUser::upsert($data, ['station_id', 'creator_type', 'creator_id']);
    }

    protected function createStations($users, $schools, $trip_id)
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

        BusinessTripStation::insert($data);
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
}
