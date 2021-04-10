<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\PartnerUser;
use App\BusinessTrip;
use App\Jobs\SendOtp;
use App\BusinessTripUser;
use Illuminate\Support\Arr;
use App\BusinessTripStation;
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
            $input = Arr::except($args, ['directive']);
            $businessTrip = $this->createBusinessTrip($input);

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

            /*
            * Revert Business Request
    
            $businessRequests = $users
                ->select('request_type','request_id')
                ->get()
                ->whereNotNull('request_type');
                
            if ($businessRequests->count()) {
                $requestType = $businessRequests
                    ->first()
                    ->request_type;

                $requestIds = $businessRequests
                    ->pluck('request_id')
                    ->toArray();

                $requestType::restore($requestIds);
            }
            */

            $users->delete();

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
            'return_time', 'days', 'duration', 'distance', 'group_chat', 'type'
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
}
