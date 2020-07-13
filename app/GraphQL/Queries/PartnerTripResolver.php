<?php

namespace App\GraphQL\Queries;

use App\User;
use App\Partner;
use App\DriverVehicle;
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
                $users = User::selectRaw('users.id, users.name, users.avatar, partner_trip_stations.name AS station_name')
                    ->where('partner_trip_users.trip_id', $args['trip_id'])
                    ->join('partner_trip_users', function ($join) {
                        $join->on('users.id', '=', 'partner_trip_users.user_id')
                            ->whereNotNull('subscription_verified_at');
                    })
                    ->leftJoin('partner_trip_stations', 'partner_trip_stations.id', '=', 'partner_trip_users.station_id')
                    ->get();

                break;
            case 'notSubscribed':
                $partnerTripUsers = PartnerTripUser::select('user_id')
                    ->where('trip_id', $args['trip_id'])
                    ->pluck('user_id');

                $users = User::Join('partner_users', 'partner_users.user_id', '=', 'users.id')
                    ->where('partner_users.partner_id', $args['partner_id'])
                    ->select('users.id', 'users.name', 'users.avatar')
                    ->whereNotIn('users.id', $partnerTripUsers)
                    ->get();

                break;
            case 'notVerified':
                $partnerTripUsers = PartnerTripUser::select('user_id')
                    ->where('trip_id', $args['trip_id'])
                    ->whereNull('subscription_verified_at')
                    ->pluck('user_id');

                $users = User::select('id', 'name', 'avatar')
                    ->whereIn('id', $partnerTripUsers)
                    ->get();
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
            ->select('partner_trips.*')
            ->get();

        return $userSubscriptions;
    }

    public function userTripPartners($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $partners = Partner::Join('partner_trips', 'partner_trips.partner_id', '=', 'partners.id')
            ->join('partner_trip_users', 'partner_trips.id', '=', 'partner_trip_users.trip_id')
            ->where('partner_trip_users.user_id', $args['user_id'])
            ->whereNotNull('partner_trip_users.subscription_verified_at')
            ->selectRaw('partners.*')
            ->distinct()
            ->get();

        return $partners;
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

    public function userTripsByPartner($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $userTrips = PartnerTrip::join('partner_trip_users', 'partner_trips.id', '=', 'partner_trip_users.trip_id')
            ->where('partner_trip_users.user_id', $args['user_id'])
            ->where('partner_trips.partner_id', $args['partner_id'])
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

    public function trip($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {   
        try {
            $trip = PartnerTrip::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Provided trip ID is not found.');
        }

        $flag = false;
        $today = strtolower(date('l'));
        $trip->schedule = $trip->schedule;
        if ($trip->schedule->$today) {
            $tripDate = date('Y-m-d') . ' ' . $trip->schedule->$today;
            $trip->date = strtotime($tripDate) * 1000;
            $trip->flag = $this->getFlag($trip->schedule->$today);
        }
        return $trip;
    }

    protected function scheduledTrips($trips) 
    {
        $sortedTrips = array();
        $days = array('saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday');
        $now = strtotime(now()) * 1000;
        $timeMargin = 60 * 30 * 1000; // 30 minutes in milliseconds

        foreach($trips as $trip) {
            foreach($days as $day) { 
                if ($trip->schedule->$day) {
                    $date = date('Y-m-d', strtotime($day));
                    $dateTime = $date . ' ' . $trip->schedule->$day;
                    $tripDate = strtotime($dateTime) * 1000;
                    $dayName = ($day == strtolower(date('l')) ? "Today" : $day);
                    
                    if ($tripDate > ($now - $timeMargin) || $trip->status) {
                        $tripInstance = new PartnerTrip();
                        $trip->date = $tripDate;
                        $trip->dayName = $dayName;
                        $trip->flag = ($tripDate - $timeMargin) < $now;
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
                        if ($tripDate > ($now - $timeMargin) || $trip->status) {
                            $tripInstance = new PartnerTrip();
                            $trip->dayName = $dayName;
                            $trip->flag = ($tripDate - $timeMargin) < $now;
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