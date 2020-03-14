<?php

namespace App\GraphQL\Queries;

use App\User;
use App\PartnerTrip;
use App\PartnerTripUser;
use App\PartnerTripStation;
use App\PartnerTripStationUser;
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
                $partnerTripUsers = PartnerTripUser::where('partner_trip_id', $args['trip_id'])
                    ->whereNotNull('subscription_verified_at')
                    ->get()->pluck('partner_user_id');

                $users = User::where('partner_id', $args['partner_id'])
                    ->whereIn('id', $partnerTripUsers)->get();
                break;
            case 'notSubscribed':
                $partnerTripUsers = PartnerTripUser::where('partner_trip_id', $args['trip_id'])
                    ->get()->pluck('partner_user_id');

                $users = User::where('partner_id', $args['partner_id'])
                    ->whereNotIn('id', $partnerTripUsers)->get();
                break;
            case 'notVerified':
                $partnerTripUsers = PartnerTripUser::where('partner_trip_id', $args['trip_id'])
                    ->whereNull('subscription_verified_at')
                    ->get()->pluck('partner_user_id');

                $users = User::where('partner_id', $args['partner_id'])
                    ->whereIn('id', $partnerTripUsers)->get();
                break;
        }

        return $users;
    }

    public function stations($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $status = $args['status'];

        switch($status) {
            case 'accepted':
                $stations = PartnerTripStation::where('partner_trip_id', $args['partner_trip_id'])
                    ->whereNotNull('accepted_at')->get();
                break;
            case 'notAccepted':
                $stations = PartnerTripStation::where('partner_trip_id', $args['partner_trip_id'])
                    ->whereNull('accepted_at')->get();
                break;
        }

        return $stations;
    }

    public function stationUsers($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $stationUsers = PartnerTripStationUser::where('station_id', $args['station_id'])
            ->join('users', 'users.id', '=', 'partner_trip_station_users.user_id')
            ->select('users.*')
            ->get();

        return $stationUsers;
    }

    public function userTrips($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $userTrips = PartnerTrip::join('partner_trip_users', 'partner_trips.id', '=', 'partner_trip_users.partner_trip_id')
            ->where('partner_trip_users.partner_user_id', $args['user_id'])
            ->whereRaw('? between startDate and endDate', [date('Y-m-d')])
            ->get();
            

        return $userTrips;
    }

    public function userMyTrips($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $userTrips = PartnerTrip::join('partner_trip_users', 'partner_trips.id', '=', 'partner_trip_users.partner_trip_id')
            ->where('partner_trip_users.partner_user_id', $args['user_id'])
            ->whereRaw('? between startDate and endDate', [date('Y-m-d')])
            ->select('partner_trips.*','partner_trip_users.partner_trip_id')
            ->get();

        $arr = array();
        foreach ($userTrips as $userTrip) {
            if ($userTrip->schedule->saturday) {
                $arr[] = [
                    "id" => $userTrip->id,
                    "name" => $userTrip->name,
                    "dayName" => "Saturday",
                    "date" => date('Y-m-d', strtotime('saturday')).' '.$userTrip->schedule->saturday
                ];
            }

            if ($userTrip->schedule->sunday) {
                $arr[] = [
                    "id" => $userTrip->id,
                    "name" => $userTrip->name,
                    "dayName" => "Sunday",
                    "date" => date('Y-m-d', strtotime('sunday')).' '.$userTrip->schedule->sunday
                ];
            }

            if ($userTrip->schedule->monday) {
                $arr[] = [
                    "id" => $userTrip->id,
                    "name" => $userTrip->name,
                    "dayName" => "Monday",
                    "date" => date('Y-m-d', strtotime('monday')).' '.$userTrip->schedule->monday
                ];
            }

            if ($userTrip->schedule->tuesday) {
                $arr[] = [
                    "id" => $userTrip->id,
                    "name" => $userTrip->name,
                    "dayName" => "Tuesday",
                    "date" => date('Y-m-d', strtotime('tuesday')).' '.$userTrip->schedule->tuesday
                ];
            }

            if ($userTrip->schedule->wednesday) {
                $arr[] = [
                    "id" => $userTrip->id,
                    "name" => $userTrip->name,
                    "dayName" => "Wednesday",
                    "date" => date('Y-m-d', strtotime('wednesday')).' '.$userTrip->schedule->wednesday
                ];
            }

            if ($userTrip->schedule->thursday) {
                $arr[] = [
                    "id" => $userTrip->id,
                    "name" => $userTrip->name,
                    "dayName" => "Thursday",
                    "date" => date('Y-m-d', strtotime('thursday')).' '.$userTrip->schedule->thursday
                ];
            }

            if ($userTrip->schedule->friday) {
                $arr[] = [
                    "id" => $userTrip->id,
                    "name" => $userTrip->name,
                    "dayName" => "Friday",
                    "date" => date('Y-m-d', strtotime('friday')).' '.$userTrip->schedule->friday
                ];
            }
        }

        usort($arr, function($element1, $element2) { 
            $datetime1 = strtotime($element1['date']); 
            $datetime2 = strtotime($element2['date']); 
            return $datetime1 - $datetime2; 
        });
        
        return $arr;
    }

    public function driverMyTrips($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $userTrips = PartnerTrip::where('driver_id', $args['driver_id'])
            ->whereRaw('? between startDate and endDate', [date('Y-m-d')])
            ->get();

        $arr = array();
        foreach ($userTrips as $userTrip) {
            if ($userTrip->schedule->saturday) {
                $arr[] = [
                    "id" => $userTrip->id,
                    "name" => $userTrip->name,
                    "dayName" => "Saturday",
                    "date" => date('Y-m-d', strtotime('saturday')).' '.$userTrip->schedule->saturday
                ];
            }

            if ($userTrip->schedule->sunday) {
                $arr[] = [
                    "id" => $userTrip->id,
                    "name" => $userTrip->name,
                    "dayName" => "Sunday",
                    "date" => date('Y-m-d', strtotime('sunday')).' '.$userTrip->schedule->sunday
                ];
            }

            if ($userTrip->schedule->monday) {
                $arr[] = [
                    "id" => $userTrip->id,
                    "name" => $userTrip->name,
                    "dayName" => "Monday",
                    "date" => date('Y-m-d', strtotime('monday')).' '.$userTrip->schedule->monday
                ];
            }

            if ($userTrip->schedule->tuesday) {
                $arr[] = [
                    "id" => $userTrip->id,
                    "name" => $userTrip->name,
                    "dayName" => "Tuesday",
                    "date" => date('Y-m-d', strtotime('tuesday')).' '.$userTrip->schedule->tuesday
                ];
            }

            if ($userTrip->schedule->wednesday) {
                $arr[] = [
                    "id" => $userTrip->id,
                    "name" => $userTrip->name,
                    "dayName" => "Wednesday",
                    "date" => date('Y-m-d', strtotime('wednesday')).' '.$userTrip->schedule->wednesday
                ];
            }

            if ($userTrip->schedule->thursday) {
                $arr[] = [
                    "id" => $userTrip->id,
                    "name" => $userTrip->name,
                    "dayName" => "Thursday",
                    "date" => date('Y-m-d', strtotime('thursday')).' '.$userTrip->schedule->thursday
                ];
            }

            if ($userTrip->schedule->friday) {
                $arr[] = [
                    "id" => $userTrip->id,
                    "name" => $userTrip->name,
                    "dayName" => "Friday",
                    "date" => date('Y-m-d', strtotime('friday')).' '.$userTrip->schedule->friday
                ];
            }
        }

        usort($arr, function($element1, $element2) { 
            $datetime1 = strtotime($element1['date']); 
            $datetime2 = strtotime($element2['date']); 
            return $datetime1 - $datetime2; 
        });
        
        return $arr;
    }

    public function userLiveTrip($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $liveTrip = PartnerTrip::join('partner_trip_users', 'partner_trips.id', '=', 'partner_trip_users.partner_trip_id')
            ->where('partner_trip_users.partner_user_id', $args['user_id'])
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

    // Comparison function 
    public function date_compare($element1, $element2) { 
        $datetime1 = strtotime($element1['datetime']); 
        $datetime2 = strtotime($element2['datetime']); 
        return $datetime1 - $datetime2; 
    }  
}
