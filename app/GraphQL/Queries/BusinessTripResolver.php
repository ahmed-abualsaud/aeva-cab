<?php

namespace App\GraphQL\Queries;

use App\User;
use App\Partner;
use App\DriverVehicle;
use App\BusinessTrip;
use App\BusinessTripUser;
use App\BusinessTripStation;
use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class BusinessTripResolver
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
                $users = User::selectRaw('users.id, users.name, users.avatar, business_trip_stations.name AS station_name')
                    ->where('business_trip_users.trip_id', $args['trip_id'])
                    ->join('business_trip_users', function ($join) {
                        $join->on('users.id', '=', 'business_trip_users.user_id')
                            ->whereNotNull('subscription_verified_at');
                    })
                    ->leftJoin('business_trip_stations', 'business_trip_stations.id', '=', 'business_trip_users.station_id')
                    ->get();

                break;
            case 'notSubscribed':
                $businessTripUsers = BusinessTripUser::select('user_id')
                    ->where('trip_id', $args['trip_id'])
                    ->pluck('user_id');

                $users = User::select('id', 'name', 'avatar')
                    ->where('partner_id', $args['partner_id'])
                    ->whereNotIn('id', $businessTripUsers)
                    ->get();

                // $users = User::Join('partner_users', 'partner_users.user_id', '=', 'users.id')
                //     ->where('partner_users.partner_id', $args['partner_id'])
                //     ->select('users.id', 'users.name', 'users.avatar')
                //     ->whereNotIn('users.id', $businessTripUsers)
                //     ->get();

                break;
            case 'notVerified':
                $businessTripUsers = BusinessTripUser::select('user_id')
                    ->where('trip_id', $args['trip_id'])
                    ->whereNull('subscription_verified_at')
                    ->pluck('user_id');

                $users = User::select('id', 'name', 'avatar')
                    ->whereIn('id', $businessTripUsers)
                    ->get();
                break;
        }

        return $users;
    }

    public function stationUsers($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $stationUsers = BusinessTripUser::where('station_id', $args['station_id'])
            ->join('users', 'users.id', '=', 'business_trip_users.user_id')
            ->select('users.*')
            ->get();

        return $stationUsers;
    }

    public function userSubscriptions($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $userSubscriptions = BusinessTrip::join('business_trip_users', 'business_trips.id', '=', 'business_trip_users.trip_id')
            ->where('business_trip_users.user_id', $args['user_id'])
            ->whereNotNull('business_trip_users.subscription_verified_at')
            ->select('business_trips.*')
            ->get();

        return $userSubscriptions;
    }

    public function userTripPartners($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $partners = Partner::Join('business_trips', 'business_trips.partner_id', '=', 'partners.id')
            ->join('business_trip_users', 'business_trips.id', '=', 'business_trip_users.trip_id')
            ->where('business_trip_users.user_id', $args['user_id'])
            ->whereNotNull('business_trip_users.subscription_verified_at')
            ->selectRaw('partners.*')
            ->distinct()
            ->get();

        return $partners;
    }
 
    public function userTrips($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $userTrips = BusinessTrip::join('business_trip_users', 'business_trips.id', '=', 'business_trip_users.trip_id')
            ->where('business_trip_users.user_id', $args['user_id'])
            ->whereNotNull('business_trip_users.subscription_verified_at')
            ->whereRaw('? between start_date and end_date', [date('Y-m-d')])
            ->select('business_trips.*')
            ->get();
        
        return $this->scheduledTrips($userTrips);
    }

    public function userTripsByPartner($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $userTrips = BusinessTrip::join('business_trip_users', 'business_trips.id', '=', 'business_trip_users.trip_id')
            ->where('business_trip_users.user_id', $args['user_id'])
            ->where('business_trips.partner_id', $args['partner_id'])
            ->whereNotNull('business_trip_users.subscription_verified_at')
            ->whereRaw('? between start_date and end_date', [date('Y-m-d')])
            ->select('business_trips.*')
            ->get();
        
        return $this->scheduledTrips($userTrips);
    }

    public function partnerLiveTrips($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return BusinessTrip::select('id', 'name')
            ->where('partner_id', $args['partner_id'])
            ->where('status', true)
            ->get();
    }

    public function driverTrips($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $driverTrips = BusinessTrip::where('driver_id', $args['driver_id'])
            ->whereRaw('? between start_date and end_date', [date('Y-m-d')])
            ->select('business_trips.*')
            ->get();

        return $this->scheduledTrips($driverTrips);
    }

    public function userLiveTrip($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $liveTrip = BusinessTrip::join('business_trip_users', 'business_trips.id', '=', 'business_trip_users.trip_id')
            ->where('business_trip_users.user_id', $args['user_id'])
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
        $liveTrip = DriverVehicle::select('trip_type', 'trip_id')
            ->where('driver_id', $args['driver_id'])
            ->where('status', 'RIDING')
            ->first();

        if ($liveTrip) {
            return [
                "status" => true,
                "tripType" => $liveTrip->trip_type,
                "tripID" => $liveTrip->trip_id
            ];
        }

        return [
            "status" => false,
            "tripType" => null,
            "tripID" => null
        ];
    }

    protected function scheduledTrips($trips) 
    {
        $sortedTrips = array();
        $days = array('saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday');
        $now = strtotime(now()) * 1000;
        $flagTimeMargin = 60 * 30 * 1000; // 30 minutes in milliseconds

        foreach($trips as $trip) {
            $tripTimeMargin = $now - ($trip->duration * 1000);
            foreach($days as $day) { 
                if ($trip->schedule->$day) {
                    $date = date('Y-m-d', strtotime($day));
                    $dateTime = $date . ' ' . $trip->schedule->$day;
                    $tripDate = strtotime($dateTime) * 1000;
                    $dayName = ($day == strtolower(date('l')) ? "Today" : $day);
                    
                    if ($tripDate > $tripTimeMargin) {
                        $tripInstance = new BusinessTrip();
                        $trip->date = $tripDate;
                        $trip->dayName = $dayName;
                        $trip->flag = ($tripDate - $flagTimeMargin) < $now;
                        $trip->isReturn = false;
                        $trip->startsAt = $tripDate > $now 
                            ? Carbon::parse($dateTime)->diffForHumans() 
                            : "Now";
                        $tripInstance->fill($trip->toArray());
                        array_push($sortedTrips, $tripInstance);
                    }

                    if ($trip->return_time) {
                        $dateTime = $date . ' ' . $trip->return_time;
                        $tripDate = strtotime($dateTime) * 1000;
                        if ($tripDate > $tripTimeMargin) {
                            $tripInstance = new BusinessTrip();
                            $trip->dayName = $dayName;
                            $trip->flag = ($tripDate - $flagTimeMargin) < $now;
                            $trip->date = $tripDate;
                            $trip->startsAt = $trip->date > $now 
                                ? Carbon::parse($dateTime)->diffForHumans() 
                                : "Now";
                            $trip->isReturn = true;
                            $tripInstance->fill($trip->toArray());
                            array_push($sortedTrips, $tripInstance);
                        }
                    }
                }
            }
        }

        usort($sortedTrips, function ($a, $b) { return ($a['date'] > $b['date']); });
        
        return $sortedTrips;
    }

    protected function getFlag($day) 
    {   
        $tripDate = Carbon::parse(date('Y-m-d') . ' ' . $day);
        $minutes = $tripDate->diffInMinutes(now());
        return ($minutes < 30) ? true : false;
    } 
}