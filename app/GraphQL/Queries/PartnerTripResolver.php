<?php

namespace App\GraphQL\Queries;

use App\User;
use App\PartnerTrip;
use App\PartnerTripUser;
use App\PartnerTripStation;
use App\PartnerTripStationUser;
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

    public function userSubscriptions($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $userSubscriptions = PartnerTrip::join('partner_trip_users', 'partner_trips.id', '=', 'partner_trip_users.partner_trip_id')
            ->where('partner_trip_users.partner_user_id', $args['user_id'])
            ->select('partner_trips.id','partner_trips.name')
            ->get();


        return $userSubscriptions;
    }

    public function userTrips($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $userTrips = PartnerTrip::join('partner_trip_users', 'partner_trips.id', '=', 'partner_trip_users.partner_trip_id')
            ->where('partner_trip_users.partner_user_id', $args['user_id'])
            ->whereRaw('? between start_date and end_date', [date('Y-m-d')])
            ->select('partner_trips.id','partner_trips.name')
            ->get();

        
        return $this->scheduledTrips($userTrips);
    }

    public function driverTrips($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $driverTrips = PartnerTrip::where('driver_id', $args['driver_id'])
            ->whereRaw('? between start_date and end_date', [date('Y-m-d')])
            ->select('id', 'name')
            ->get();

        return $this->scheduledTrips($driverTrips);
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
    

    protected function scheduledTrips($trips) {

        $arr = [];
        $days = ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        foreach ($trips as $trip) {
            foreach($days as $day) {
                if ($trip->schedule->$day) {
                    $date = date('Y-m-d', strtotime($day)).' '.$trip->schedule->$day;
                    $arr[] = [
                        "id" => $trip->id,
                        "name" => $trip->name,
                        "dayName" => $day,
                        "date" => $date,
                        "startsAt" => Carbon::parse($date)->diffForHumans()
                    ];
                }
            }
        }

        usort($arr, function($element1, $element2) { 
            $datetime1 = strtotime($element1['date']); 
            $datetime2 = strtotime($element2['date']); 
            return $datetime1 - $datetime2; 
        });
        
        return $arr;
    }
}
