<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\PartnerUser;
use App\BusinessTrip;
use App\Jobs\SendOtp;
use App\DriverVehicle;
use App\SchoolRequest;
use App\BusinessTripUser;
use App\Mail\DefaultMail;
use Illuminate\Support\Arr;
use App\BusinessTripStation;
use App\BusinessTripSchedule;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BusinessTripResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        $tripInput = $this->tripInput($args);
        $businessTrip = BusinessTrip::create($tripInput);

        $businessTrip->update(['subscription_code' => Hashids::encode($businessTrip->id)]);
         
        $scheduleInput = $this->scheduleInput($args);

        $scheduleInput['trip_id'] = $businessTrip->id; 
        BusinessTripSchedule::create($scheduleInput);

        if (array_key_exists('request_ids', $args) && $args['request_ids']) {
            $this->createStationsAndAssignUsers($args, $businessTrip->id);
        }

        return $businessTrip;
    }

    public function addSchoolRequest($_, array $args)
    {
        if (array_key_exists('station_id', $args) && $args['station_id']) {
            return $this->assignUsersToStation($args);
        } 
        
        return $this->createStationsAndAssignUsers($args, $args['trip_id']);
    }

    public function update($_, array $args)
    {
        $tripInput = $this->tripInput($args);
        try {
            $trip = BusinessTrip::findOrFail($args['id']);
            $trip->update($tripInput);
        } catch (ModelNotFoundException $e) {
            throw new CustomException('Trip with the provided ID is not found.');
        }
        
        $scheduleInput = $this->scheduleInput($args);
        try {
            $tripSchedule = BusinessTripSchedule::findOrFail($trip->schedule->id);
            $tripSchedule->update($scheduleInput);
        } catch (ModelNotFoundException $e) {
            $scheduleInput['trip_id'] = $trip->id;
            BusinessTripSchedule::create($scheduleInput);
        }
    
        return $trip;
    }

    public function updateStatus($_, array $args)
    {
        try {
            $trip = BusinessTrip::findOrFail($args['id']);
            $driverStatus = DriverVehicle::where('driver_id', $trip->driver_id)
                ->where('vehicle_id', $trip->vehicle_id);
            $usersStatus = BusinessTripUser::where('trip_id', $trip->id);
        } catch (\Exception $e) {
            throw new CustomException('We could not find a trip with the provided ID.');
        }

        $tripInput = ["status" => $args['status'], "log_id" => $args['log_id']];

        if ($args['status']) {
            $driverStatusInput = [
                "status" => "RIDING",
                "trip_type" => "App\BusinessTrip",
                "trip_id" => $args['id']
            ];
        } else {
            $driverStatusInput = [
                "status" => "ACTIVE",
                "trip_type" => null,
                "trip_id" => null
            ];
        }

        $usersStatusInput = ["is_arrived" => false, "is_picked" => false];

        $trip->update($tripInput);
        $driverStatus->update($driverStatusInput);
        $usersStatus->update($usersStatusInput);

        return $trip;
    }

    public function inviteUser($_, array $args)
    {
        $data = [];
        $arr = [];

        foreach($args['user_id'] as $val) {
            $arr['trip_id'] = $args['trip_id'];
            $arr['user_id'] = $val;
            $arr['created_at'] = $arr['updated_at'] = now();
            array_push($data, $arr);
        } 

        try {
            BusinessTripUser::insert($data);
        } catch (\Exception $e) {
            throw new CustomException('Each user is allowed to subscribe for a trip once.');
        }

        $users = User::select('phone', 'email')
            ->whereIn('id', $args['user_id'])
            ->get();
        $phones = $users->pluck('phone')->toArray();
        $emails = $users->pluck('email');

        $message = 'Dear valued user, kindly use this code to confirm your subscription: ' . $args['subscription_code'];
        
        Mail::bcc($emails)->send(new DefaultMail($message, "Trip Subscription Code"));
        SendOtp::dispatch(implode(",", $phones), $message); 

        return [
            "status" => true,
            "message" => "Subscription code has been sent."
        ];
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
                throw new CustomException('You have already subscribed for this trip.');
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
            BusinessTripUser::where('trip_id', $args['trip_id'])
                ->whereIn('user_id', $args['user_id'])
                ->delete();
        } catch (\Exception $e) {
            throw new CustomException('Subscription cancellation failed.');
        }
        
        return [
            "status" => true,
            "message" => "Subscription cancellation has done successfully."
        ];
    }

    protected function tripInput(array $args)
    {
        return Arr::only($args, ['name', 'partner_id', 'driver_id', 'vehicle_id', 'start_date', 'end_date', 'return_time']);
    }

    protected function scheduleInput(array $args)
    {
        return Arr::only($args, ['saturday','sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
    }

    protected function createStationsAndAssignUsers($args, $trip_id)
    {
        DB::beginTransaction();
        try {
            $schoolData = []; $schoolArr = [];
            foreach($args['schools'] as $val) {
                $schoolArr['trip_id'] = $trip_id;
                $schoolArr['name'] = $val['name'];
                $schoolArr['latitude'] = $val['lat'];
                $schoolArr['longitude'] = $val['lng'];
                $schoolArr['state'] = 'END';
                $schoolArr['created_at'] = $schoolArr['updated_at'] = $schoolArr['accepted_at'] = now();
                array_push($schoolData, $schoolArr);
            } 
            BusinessTripStation::insert($schoolData);
            
            $tripUserData = []; $tripUserArr = [];

            foreach($args['users'] as $user) {
                $tripStationData = [
                    'creator_type' => 'App\\SchoolRequest',
                    'creator_id' => $user['request_id'],
                    'trip_id' => $trip_id,
                    'name' => $user['address'],
                    'latitude' => $user['lat'],
                    'longitude' => $user['lng'],
                    'state' => 'PICKABLE',
                    'accepted_at' => now()
                ];

                $tripStation = BusinessTripStation::create($tripStationData);

                $tripUserArr['trip_id'] = $trip_id;
                $tripUserArr['user_id'] = $user['id'];
                $tripUserArr['station_id'] = $tripStation->id;
                $tripUserArr['subscription_verified_at'] 
                    = $tripUserArr['created_at'] 
                    = $tripUserArr['updated_at'] 
                    = now();
                array_push($tripUserData, $tripUserArr);
            }

            BusinessTripUser::insert($tripUserData);
            SchoolRequest::accept($args['request_ids']);

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException('We could not able to create stations and assign users'.$e->getMessage());
        }
    }

    protected function assignUsersToStation(array $args)
    {
        DB::beginTransaction();
        try {
            $tripUserData = []; $tripUserArr = [];
            foreach($args['users'] as $user) {
                $tripUserArr['trip_id'] = $args['trip_id'];
                $tripUserArr['user_id'] = $user['id'];
                $tripUserArr['station_id'] = $args['station_id'];
                $tripUserArr['subscription_verified_at'] 
                    = $tripUserArr['created_at'] 
                    = $tripUserArr['updated_at'] 
                    = now();
                array_push($tripUserData, $tripUserArr);
            }
    
            BusinessTripUser::insert($tripUserData);
            SchoolRequest::accept($args['request_ids']);

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException('We could not able to assign users to specified station');
        }
    }
}
