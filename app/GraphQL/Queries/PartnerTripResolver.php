<?php

namespace App\GraphQL\Queries;

use App\User;
use App\PartnerTrip;
use App\PartnerTripUser;
use App\PartnerTripStation;
use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

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

    public function users($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $status = $args['status'];

        switch($status) {
            case 'subscribed':
                $partnerTripUsers = PartnerTripUser::where('trip_id', $args['trip_id'])
                    ->whereNotNull('subscription_verified_at')
                    ->get()->pluck('user_id');

                $users = User::where('partner_id', $args['partner_id'])
                    ->whereIn('id', $partnerTripUsers)->get();
                break;
            case 'notSubscribed':
                $partnerTripUsers = PartnerTripUser::where('trip_id', $args['trip_id'])
                    ->get()->pluck('user_id');

                $users = User::where('partner_id', $args['partner_id'])
                    ->whereNotIn('id', $partnerTripUsers)->get();
                break;
            case 'notVerified':
                $partnerTripUsers = PartnerTripUser::where('trip_id', $args['trip_id'])
                    ->whereNull('subscription_verified_at')
                    ->get()->pluck('user_id');

                $users = User::where('partner_id', $args['partner_id'])
                    ->whereIn('id', $partnerTripUsers)->get();
                break;
        }

        return $users;
    }

    public function stationUsers($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $stationUsers = PartnerTripUser::where('station_id', $args['station_id'])
            ->join('users', 'users.id', '=', 'partner_trip_users.user_id')
            ->select('users.*')
            ->get();

        return $stationUsers;
    }

    public function userSubscriptions($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $userSubscriptions = PartnerTrip::join('partner_trip_users', 'partner_trips.id', '=', 'partner_trip_users.trip_id')
            ->where('partner_trip_users.user_id', $args['user_id'])
            ->whereNotNull('partner_trip_users.subscription_verified_at')
            ->get();

        return $userSubscriptions;
    }

    public function userTrips($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $userTrips = PartnerTrip::join('partner_trip_users', 'partner_trips.id', '=', 'partner_trip_users.trip_id')
            ->where('partner_trip_users.user_id', $args['user_id'])
            ->whereNotNull('partner_trip_users.subscription_verified_at')
            ->whereRaw('? between start_date and end_date', [date('Y-m-d')])
            ->select('partner_trips.*')
            ->get();
        
        return $this->scheduledTrips($userTrips);
    }

    public function driverTrips($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $driverTrips = PartnerTrip::where('driver_id', $args['driver_id'])
            ->whereRaw('? between start_date and end_date', [date('Y-m-d')])
            ->select('partner_trips.*')
            ->get();

        return $this->scheduledTrips($driverTrips);
    }

    public function userLiveTrip($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $liveTrip = PartnerTrip::join('partner_trip_users', 'partner_trips.id', '=', 'partner_trip_users.trip_id')
            ->where('partner_trip_users.user_id', $args['user_id'])
            ->where('status', true)
            ->first();

        if ($liveTrip) {
            return [
                "status" => true,
                "trip" => $liveTrip
            ];
        }

        return [
            "status" => false,
            "trip" => null
        ];
    }

    public function driverLiveTrip($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $liveTrip = PartnerTrip::where('driver_id', $args['driver_id'])
            ->where('status', true)
            ->first();

        if ($liveTrip) {
            return [
                "status" => true,
                "trip" => $liveTrip
            ];
        }

        return [
            "status" => false,
            "trip" => null
        ];
    } 

    public function trip($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {   
        try {
            $trip = PartnerTrip::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Provided trip ID is not found.');
        }

        $flag = false;
        $startsAt = 'This trip will not start today';
        $today = strtolower(date('l'));
        if ($trip->schedule->$today) {
            $date = date('Y-m-d') . ' ' . $trip->schedule->$today;
            $flag = $this->getFlag($trip->schedule->$today);
            $startsAt = Carbon::parse($date)->diffForHumans();
            $trip->startsAt = $startsAt;
        }
        $trip->flag = $flag;
        $trip->startsAt = $startsAt;
        return $trip;
    }

    protected function scheduledTrips($trips) {

        $sortedTrips = array();
        $days = array('saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday');
        $upcomingCheck = true;
        $now = strtotime(now())*1000;

        foreach($trips as $trip) {
            foreach($days as $day) {
                if ($trip->schedule->$day) {
                    $tripInstance = new PartnerTrip();
                    $date = date('Y-m-d', strtotime($day));
                    $dateTime = $date . ' ' . $trip->schedule->$day;  
                    $trip->dayName = $day;
                    $trip->date = strtotime($dateTime) * 1000;
                    $trip->flag = $this->getFlag($trip->schedule->$day);
                    $trip->startsAt = Carbon::parse($dateTime)->diffForHumans();
                    $tripInstance->fill($trip->toArray());
                    array_push($sortedTrips, $tripInstance);
                }
            }
        }

        usort($sortedTrips, function ($a, $b) { return ($a['date'] > $b['date']); });

        foreach($sortedTrips as $trip) {
            if ($upcomingCheck && $now < $trip->date) {
                $trip->upcoming = true;
                $upcomingCheck = false;
            } else {
                $trip->upcoming = false;
            }
        }
        
        return $sortedTrips;
    }

    protected function getFlag($day) 
    {   
        $tripDate = Carbon::parse(date('Y-m-d') . ' ' . $day);
        $minutes = $tripDate->diffInMinutes(now());
        return ($minutes < 30) ? true : false;
    } 
}