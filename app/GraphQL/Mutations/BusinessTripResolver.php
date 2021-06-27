<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\PartnerUser;
use App\BusinessTrip;
use App\Jobs\SendOtp;
use App\BusinessTripSubscription;
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
            throw new CustomException(__('lang.create_trip_failed'));
        }

        return $businessTrip;
    }

    public function update($_, array $args)
    {
        try {
            $tripInput = Arr::except($args, ['directive']);
            $trip = BusinessTrip::findOrFail($args['id']);
            $trip->update($tripInput);
        } catch (ModelNotFoundException $e) {
            throw new CustomException(__('lang.trip_not_found'));
        }

        return $trip;
    }

    public function updateRoute($_, array $args)
    {
        try {
            
            $cases = []; $ids = []; $distance = []; $duration = []; $order = [];

            foreach ($args['stations'] as $value) {
                $id = (int) $value['id'];
                $cases[] = "WHEN {$id} then ?";
                $distance[] = $value['distance'];
                $duration[] = $value['duration'];
                $order[] = $value['order'];
                $ids[] = $id;
            }

            $ids = implode(',', $ids);
            $cases = implode(' ', $cases);
            $params = array_merge($distance, $duration, $order);

            DB::update("UPDATE `business_trip_stations` SET 
                `distance` = CASE `id` {$cases} END, 
                `duration` = CASE `id` {$cases} END, 
                `order` = CASE `id` {$cases} END
                WHERE `id` in ({$ids})", $params);

            $total = end($args['stations']);

            BusinessTrip::where('id', $args['trip_id'])
                ->update([
                    'route' => $args['route'], 
                    'distance' => $total['distance'], 
                    'duration' => $total['duration']
                ]);
            
            return ['distance' => $total['distance'], 'duration' => $total['duration']];
            
        } catch (\Exception $e) {
            throw new CustomException(__('lang.update_route_failed'));
        }
    }

    public function copy($_, array $args)
    {
        DB::beginTransaction();
        try {
            $trip = $this->createTripCopy($args);

            if ($args['include_stations'])
                $this->createStationsCopy($args['id'], $trip->id);

            if ($args['include_subscriptions'])
                $this->createSubscriptionsCopy($args['id'], $trip->id);

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException(__('lang.copy_trip_failed'));
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

            BusinessTripSubscription::insert($data);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.invite_user_failed'));
        }

        $this->notifyUserViaSms($args);

        return __('lang.user_invited_not_verified');
    }

    public function createSubscription($_, array $args)
    {
        try {
            $arr = [
                'trip_id' => $args['trip_id'],
                'station_id' => $args['station_id'],
                'destination_id' => $args['destination_id'],
                'created_at' => now(), 
                'updated_at' => now(),
                'subscription_verified_at' => now(),
                'payable' => $args['payable'],
                'due_date' => date('Y-m-d')
            ];

            foreach($args['user_id'] as $val) {
                $arr['user_id'] = $val;
                $data[] = $arr;
            } 

            BusinessTripSubscription::upsert($data, ['station_id', 'destination_id', 'payable', 'updated_at']);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.subscribe_user_failed'));
        }

        return __('lang.subscribe_user');
    }

    public function confirmSubscription($_, array $args) 
    {
        try {
            $trip_id = Hashids::decode($args['subscription_code']);
            $trip = BusinessTrip::findOrFail($trip_id[0]);
        } catch (\Exception $e) {
            throw new CustomException('Subscription code is not valid.');
        }
        
        try {
            $tripUser = BusinessTripSubscription::where('trip_id', $trip->id)
                ->where('user_id', $args['user_id'])
                ->firstOrFail();
            if ($tripUser->subscription_verified_at) {
                throw new CustomException(__('lang.already_subscribed'));
            } else {
                $tripUser->update([
                    'subscription_verified_at' => now(),
                    'payable' => $trip->price,
                    'due_date' => date('Y-m-d')
                ]);
            }
        } catch (ModelNotFoundException $e) {
            BusinessTripSubscription::create([
                'trip_id' => $trip->id,
                'user_id' => $args['user_id'],
                'subscription_verified_at' => now(),
                'due_date' => date('Y-m-d'),
                'payable' => $trip->price
            ]);

            PartnerUser::firstOrCreate([
                'partner_id' => $trip->partner_id, 
                'user_id' => $args['user_id']
            ]);
        }
        
        return $trip;
    }

    public function deleteSubscription($_, array $args)
    {
        try {
            return BusinessTripSubscription::where('trip_id', $args['trip_id'])
                ->whereIn('user_id', $args['user_id'])
                ->delete();

        } catch (\Exception $e) {
            throw new CustomException(__('lang.cancel_subscribe_failed'));
        }
    }

    public function verifySubscription($_, array $args)
    {
        try {
            return BusinessTripSubscription::where('trip_id', $args['trip_id'])
                ->where('user_id', $args['user_id'])
                ->update(['subscription_verified_at' => $args['subscription_verified_at']]);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.toggle_subscribe_failed'));
        }
    }

    protected function notifyUserViaSms(array $args)
    {
        try {
            $phones = User::select('phone')
                ->whereIn('id', $args['user_id'])
                ->pluck('phone')
                ->toArray();

            $message = __('lang.subscription_code', 
                [
                    'trip_name' => $args['trip_name'],
                    'subscription_code' => $args['subscription_code'],
                ]);
            
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
            'partner_id', 'driver_id', 'vehicle_id', 'start_date', 'end_date', 'return_time', 
            'days', 'duration', 'distance', 'group_chat', 'route', 'price', 'type'
            )
            ->findOrFail($args['id'])
            ->toArray();

        $originalTrip['name'] = $args['name'];
        $originalTrip['name_ar'] = $args['name_ar'];
        
        return $this->createBusinessTrip($originalTrip);
    }

    protected function createStationsCopy($oldTripId, $newTripId)
    {
        $originalStations = BusinessTripStation::select(
            'name', 'name_ar', 'latitude', 'longitude', 'duration', 'distance', 'state', 'accepted_at', 'order'
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
        $originalSubscriptions = BusinessTripSubscription::select('user_id')
            ->where('trip_id', $oldTripId)
            ->get();

        foreach($originalSubscriptions as $subscription) {
            $subscription->trip_id = $newTripId;
            $subscription->created_at = now();
            $subscription->updated_at = now();
            $subscription->subscription_verified_at = now();
            $subscription->due_date = date('Y-m-d');
        }

        return BusinessTripSubscription::insert($originalSubscriptions->toArray());
    }
}
