<?php

namespace App\GraphQL\Queries;

use App\User;
use Carbon\Carbon;
use App\BusinessTrip;

class BusinessTripResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function users($_, array $args)
    {
        $status = $args['status'];

        switch($status) {
            case 'subscribed':
                $users = User::selectRaw(
                    'users.id, users.name, users.avatar, users.phone, 
                    station.id AS station_id, station.name AS station_name, 
                    destination.id AS destination_id, destination.name AS destination_name, 
                    subscription.subscription_verified_at'
                    )
                    ->join(
                        'business_trip_users as subscription', 
                        'subscription.user_id', '=', 'users.id'
                    )
                    ->leftJoin(
                        'business_trip_stations as station', 
                        'station.id', '=', 'subscription.station_id'
                    )
                    ->leftJoin(
                        'business_trip_stations as destination', 
                        'destination.id', '=', 'subscription.destination_id'
                    )
                    ->where('subscription.trip_id', $args['trip_id'])
                    ->get();

                break;
            case 'notSubscribed':
                $users = User::select('users.id', 'users.name', 'users.avatar', 'users.phone')
                    ->join('partner_users', 'users.id', '=', 'partner_users.user_id')
                    ->where('partner_users.partner_id', $args['partner_id'])
                    ->whereNotIn('partner_users.user_id', function($query) use ($args) {
                        $query->select('user_id')
                            ->from('business_trip_users')
                            ->where('trip_id', $args['trip_id']);
                    })
                    ->get();
                break;
        }

        return $users;
    }

    public function stationUsers($_, array $args)
    {
        $users = User::select('users.id', 'users.name', 'users.avatar', 'users.phone')
            ->join('business_trip_users', 'business_trip_users.user_id', '=', 'users.id');

            if ($args['status'] == 'assigned') {
                $users = $users->where('station_id', $args['station_id']);
            } else {
                $users = $users->where('trip_id', $args['trip_id'])
                    ->where(function ($query) use ($args) {
                        $query->whereNull('station_id')
                            ->orWhere('station_id', '<>', $args['station_id']);
                });
            }

        return $users->get();
    }

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

        $userTrips = BusinessTrip::join('business_trip_users', 'business_trips.id', '=', 'business_trip_users.trip_id')
            ->where('business_trip_users.user_id', $args['user_id']);

        $userTrips = $userTrips->whereNotNull('business_trip_users.subscription_verified_at')
            ->whereRaw('? between start_date and end_date', [date('Y-m-d')])
            ->whereRaw('JSON_EXTRACT(business_trips.days, "$.'.$args['day'].'") <> CAST("null" AS JSON)')
            ->where(function ($query) use ($args) {
                $query->whereNull('business_trip_schedules.days')
                    ->orWhere('business_trip_schedules.days->'.$args['day'], true);
            })
            ->selectRaw(
                'business_trips.*,
                business_trip_attendance.date AS absence_date'
            )
            ->leftJoin('business_trip_attendance', function ($join) use ($args, $date) {
                $join->on('business_trips.id', '=', 'business_trip_attendance.trip_id')
                    ->where('business_trip_attendance.user_id', $args['user_id'])
                    ->where('business_trip_attendance.is_absent', true)
                    ->whereDate('business_trip_attendance.date', $date);
            })
            ->leftJoin('business_trip_schedules', function ($join) use ($args) {
                $join->on('business_trips.id', '=', 'business_trip_schedules.trip_id')
                    ->where('business_trip_schedules.user_id', $args['user_id']);
            })
            ->get();

        if ($userTrips->isEmpty()) return [];
        
        return $this->scheduledTrips($userTrips, $args['day']);
    }

    public function driverTrips($_, array $args)
    {

        $driverTrips = BusinessTrip::where('driver_id', $args['driver_id'])
            ->whereRaw('? between start_date and end_date', [date('Y-m-d')])
            ->whereRaw('JSON_EXTRACT(days, "$.'.$args['day'].'") <> CAST("null" AS JSON)')
            ->get();

        if ($driverTrips->isEmpty()) return [];

        return $this->scheduledTrips($driverTrips, $args['day'], 'driver');
    }

    public function userLiveTrips($_, array $args)
    {
        $today = strtolower(date('l'));

        $liveTrips = BusinessTrip::join('business_trip_users', 'business_trips.id', '=', 'business_trip_users.trip_id')
            ->where('business_trip_users.user_id', $args['user_id'])
            ->where('status', true)
            ->whereRaw('JSON_EXTRACT(business_trips.days, "$.'.$today.'") <> CAST("null" AS JSON)')
            ->where(function ($query) use ($today) {
                $query->whereNull('business_trip_schedules.days')
                    ->orWhere('business_trip_schedules.days->'.$today, true);
            })
            ->select('business_trips.*')
            ->leftJoin('business_trip_schedules', function ($join) use ($args) {
                $join->on('business_trips.id', '=', 'business_trip_schedules.trip_id')
                    ->where('business_trip_schedules.user_id', $args['user_id']);
            })
            ->get();

        return $liveTrips;
    }

    public function driverLiveTrip($_, array $args)
    {
        try {
            $liveTrip = BusinessTrip::select('id')
                ->where('driver_id', $args['driver_id'])
                ->where('status', true)
                ->firstOrFail();
            return ["status" => true, "tripType" => "App\BusinessTrip", "tripID" => $liveTrip->id];
        } catch (\Exception $e) {
            return ["status" => false, "tripType" => null, "tripID" => null];
        }
    }

    public function driverLiveTrips($_, array $args)
    {
        $liveTrips = BusinessTrip::where('driver_id', $args['driver_id'])
            ->where('status', true)
            ->get();

        return $liveTrips;
    }

    protected function scheduledTrips($trips, $day, $for = null) 
    {
        $dateTime = date('Y-m-d', strtotime($day));
        
        foreach($trips as $trip) {
            $trip->dayName = $day;
            $trip->is_absent = $trip->absence_date === $dateTime;
            $tripInstance = new BusinessTrip();
            $trip->date = strtotime($dateTime.' '.$trip->days[$day]) * 1000;
            if ($for === 'driver') $trip->flag = $this->getFlag($trip->days[$day]);
            $trip->isReturn = false;
            $trip->startsAt = Carbon::parse($dateTime.' '.$trip->days[$day])->format('h:i a');
            $tripInstance->fill($trip->toArray());
            $sortedTrips[] = $tripInstance;
            if ($trip->return_time) {
                $tripInstance = new BusinessTrip();
                $trip->date = strtotime($dateTime.' '.$trip->return_time) * 1000;;
                if ($for === 'driver') $trip->flag = $this->getFlag($trip->return_time);
                $trip->startsAt = Carbon::parse($dateTime.' '.$trip->return_time)->format('h:i a');
                $trip->isReturn = true;
                $tripInstance->fill($trip->toArray());
                $sortedTrips[] = $tripInstance;
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