<?php

namespace App\GraphQL\Queries;

use App\BusinessTrip;

class BusinessTripResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function userSubscriptions($_, array $args)
    {
        $userSubscriptions = BusinessTrip::join('business_trip_users', 'business_trips.id', '=', 'business_trip_users.trip_id')
            ->where('business_trip_users.user_id', $args['user_id'])
            ->whereNotNull('business_trip_users.subscription_verified_at')
            ->select('business_trips.*')
            ->get();

        return $userSubscriptions;
    }

    public function userTrips($_, array $args)
    {
        $date = date('Y-m-d', strtotime($args['day']));

        $userTrips = BusinessTrip::selectRaw(
            'business_trips.id, business_trips.name, business_trips.days,
            business_trip_attendance.date AS absence_date'
        )
        ->join('business_trip_users', 'business_trips.id', '=', 'business_trip_users.trip_id')
        ->where('business_trip_users.user_id', $args['user_id'])
        ->whereNotNull('business_trip_users.subscription_verified_at')
        ->whereRaw('? between start_date and end_date', [date('Y-m-d')])
        ->whereRaw('JSON_EXTRACT(business_trips.days, "$.'.$args['day'].'") <> CAST("null" AS JSON)')
        ->where(function ($query) use ($args) {
            $query->whereNull('business_trip_schedules.days')
                ->orWhere('business_trip_schedules.days->'.$args['day'], true);
        })
        ->leftJoin('business_trip_attendance', function ($join) use ($args, $date) {
            $join->on('business_trips.id', '=', 'business_trip_attendance.trip_id')
                ->where('business_trip_attendance.user_id', $args['user_id'])
                ->where('business_trip_attendance.is_absent', true)
                ->where('business_trip_attendance.date', $date);
        })
        ->leftJoin('business_trip_schedules', function ($join) use ($args) {
            $join->on('business_trips.id', '=', 'business_trip_schedules.trip_id')
                ->where('business_trip_schedules.user_id', $args['user_id']);
        })
        ->get();

        if ($userTrips->isEmpty()) return [];

        return $this->schedule($userTrips, $args['day']);
    }

    public function userLiveTrips($_, array $args)
    {
        $today = strtolower(date('l'));

        return BusinessTrip::selectRaw('business_trips.id, business_trips.name')
            ->join('business_trip_users', 'business_trips.id', '=', 'business_trip_users.trip_id')
            ->where('business_trip_users.user_id', $args['user_id'])
            ->whereNotNull('log_id')
            ->whereRaw('JSON_EXTRACT(business_trips.days, "$.'.$today.'") <> CAST("null" AS JSON)')
            ->where(function ($query) use ($today) {
                $query->whereNull('business_trip_schedules.days')
                    ->orWhere('business_trip_schedules.days->'.$today, true);
            })
            ->leftJoin('business_trip_schedules', function ($join) use ($args) {
                $join->on('business_trips.id', '=', 'business_trip_schedules.trip_id')
                    ->where('business_trip_schedules.user_id', $args['user_id']);
            })
            ->get();
    }

    public function driverTrips($_, array $args)
    {
        $driverTrips = BusinessTrip::select('id', 'name')
            ->where('driver_id', $args['driver_id'])
            ->whereRaw('? between start_date and end_date', [date('Y-m-d')])
            ->whereRaw('JSON_EXTRACT(days, "$.'.$args['day'].'") <> CAST("null" AS JSON)')
            ->get();

        if ($driverTrips->isEmpty()) return [];

        return $this->schedule($driverTrips, $args['day']);
    }

    public function driverLiveTrips($_, array $args)
    {
        $liveTrips = BusinessTrip::select('id', 'name')
            ->where('driver_id', $args['driver_id'])
            ->whereNotNull('log_id')
            ->get();

        return $liveTrips;
    }

    protected function schedule($trips, $day) 
    {
        $dateTime = date('Y-m-d', strtotime($day));
        
        foreach($trips as $trip) {
            $trip->is_absent = $trip->absence_date === $dateTime;
            $trip->starts_at = $dateTime.' '.$trip->days[$day];
        }
        
        return $trips->sortBy('starts_at');;
    }
}