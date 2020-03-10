<?php

namespace App\GraphQL\Mutations;

use App\PartnerTrip;
use App\PartnerTripSchedule;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        try {
            $tripInput = Arr::only($args, ['name', 'partner_id', 'driver_id', 'vehicle_id', 'ride_car_share', 'location', 'startDate', 'endDate', 'return_time']);
            $newTrip = PartnerTrip::create($tripInput);
            $subscriptionCode = 'PRTNR' . $newTrip->partner_id . 'TRP' . $newTrip->id;
            $newTrip->update(['subscription_code' => $subscriptionCode]);
        } catch (\Exception $e) {
            throw new \Exception('Trip not created. Something went wrong.');
        }
        
        try {
            $scheduleInput = Arr::only($args, ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
            $scheduleInput['partner_trip_id'] = $newTrip->id;
            PartnerTripSchedule::create($scheduleInput);
        } catch (\Exception $e) {
            throw new \Exception('Trip schedule not created. Something went wrong.');
        }
    
        return $newTrip;
    }

    public function update($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $tripInput = Arr::only($args, ['name', 'partner_id', 'driver_id', 'vehicle_id', 'ride_car_share', 'location', 'startDate', 'endDate', 'return_time']);
            $trip = PartnerTrip::findOrFail($args['id']);
            $trip->update($tripInput);
        } catch (\Exception $e) {
            throw new \Exception('Trip not updated. Something went wrong.');
        }
        
        try {
            $scheduleInput = Arr::only($args, ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
            $tripSchedule = PartnerTripSchedule::findOrFail($trip->schedule->id);
            $tripSchedule->update($scheduleInput);
        } catch (ModelNotFoundException $e) {
            $scheduleInput['partner_trip_id'] = $trip->id;
            PartnerTripSchedule::create($scheduleInput);
        }
    
        return $trip;
    }
}
