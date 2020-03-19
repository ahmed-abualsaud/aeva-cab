<?php

namespace App\GraphQL\Mutations;

use App\PartnerTrip;
use App\PartnerTripSchedule;
use App\PartnerTripUser;
use App\User;
use App\Notifications\TripSubscription;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Notification;

class PartnerTripResolver
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
        $tripInput = $this->tripInput($rootValue, $args, $context, $resolveInfo);
        $newTrip = PartnerTrip::create($tripInput);

        $subscriptionCode = 'PRTNR' . $newTrip->partner_id . 'TRP' . $newTrip->id;
        $newTrip->update(['subscription_code' => $subscriptionCode]);
        
        $scheduleInput = $this->scheduleInput($rootValue, $args, $context, $resolveInfo);

        $scheduleInput['partner_trip_id'] = $newTrip->id;
        PartnerTripSchedule::create($scheduleInput);

        return $newTrip;
    }

    public function update($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $tripInput = $this->tripInput($rootValue, $args, $context, $resolveInfo);
        try {
            $trip = PartnerTrip::findOrFail($args['id']);
            $trip->update($tripInput);
        } catch (\Exception $e) {
            throw new \Exception('Trip with the provided ID is not found.');
        }
        
        $scheduleInput = $this->scheduleInput($rootValue, $args, $context, $resolveInfo);
        try {
            $tripSchedule = PartnerTripSchedule::findOrFail($trip->schedule->id);
            $tripSchedule->update($scheduleInput);
        } catch (ModelNotFoundException $e) {
            $scheduleInput['partner_trip_id'] = $trip->id;
            PartnerTripSchedule::create($scheduleInput);
        }
    
        return $trip;
    }

    public function inviteUser($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $users = User::select(['phone', 'email'])->whereIn('id', $args['partner_user_id'])->get();
        $phones = $users->pluck('phone');
        $emails = $users->pluck('email');

        Notification::route('mail', $emails)
            ->notify(new TripSubscription($args['subscription_code']));

        $data = [];
        $arr = [];

        foreach($args['partner_user_id'] as $val) {
            $arr['partner_trip_id'] = $args['partner_trip_id'];
            $arr['partner_user_id'] = $val;
            array_push($data, $arr);
        } 

        try {
            PartnerTripUser::insert($data);
        } catch (\Exception $e) {
            throw new \Exception('Each user is allowed to subscribe for a trip once.');
        }

        return [
            "status" => true,
            "message" => "Subscription code has been sent."
        ];
    }

    public function subscribeUser($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo) 
    {
        try {
            $trip = PartnerTrip::where('subscription_code', $args['subscription_code'])->firstOrFail();
        } catch (\Exception $e) {
            throw new \Exception('The provided subscription code is invalid.');
        }
        
        try {
            $tripUser = PartnerTripUser::where('partner_trip_id', $trip['id'])
                ->where('partner_user_id', $args['user_id'])->firstOrFail();
            if ($tripUser->subscription_verified_at) {
                throw new \Exception('You have already subscribed for this trip.');
            } else {
                $tripUser->update(['subscription_verified_at' => now()]);
            }
        } catch (\Exception $e) {
            PartnerTripUser::create([
                'partner_trip_id' => $trip['id'],
                'partner_user_id' => $args['user_id'],
                'subscription_verified_at' => now()
            ]);
            User::where('id', $args['user_id'])->update(['partner_id' => $trip['partner_id']]);
        }
        
        return $trip;
    }

    public function unsubscribeUser($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        PartnerTripUser::where('partner_trip_id', $args['partner_trip_id'])
        ->whereIn('partner_user_id', $args['partner_user_id'])
        ->delete();

      return [
        "status" => true,
        "message" => "Selected subscriptions have been cancelled successfully."
      ];
    }

    protected function tripInput($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return Arr::only($args, ['name', 'partner_id', 'driver_id', 'vehicle_id', 'ride_car_share', 'location', 'start_date', 'end_date', 'return_time', 'd_latitude', 'd_longitude']);
    }

    protected function scheduleInput($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return Arr::only($args, ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
    }
}
