<?php

namespace App\GraphQL\Queries;

use App\SeatsTrip;

class SeatsTripResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function userLiveTrips($_, array $args)
    {
        $today = strtolower(date('l'));

        return SeatsTrip::join('seats_trip_bookings as b', 'b.trip_id', '=', 'seats_trips.id')
            ->where('date', $today)
            ->where('user_id', $args['user_id'])
            ->whereNotNull('log_id')
            ->where('status', 'CONFIRMED')
            ->get();
    }

    public function driverTrips($_, array $args)
    {
        $driverTrips = SeatsTrip::select('id', 'name', 'name_ar', 'days')
            ->where('driver_id', $args['driver_id'])
            ->whereRaw('? between start_date and end_date', [date('Y-m-d')])
            ->whereRaw('JSON_EXTRACT(days, "$.'.$args['day'].'") <> CAST("null" AS JSON)')
            ->get();

        if ($driverTrips->isEmpty()) return [];

        return $this->schedule($driverTrips, $args['day']);
    }

    public function driverLiveTrips($_, array $args)
    {
        $liveTrips = SeatsTrip::select('id', 'name', 'name_ar')
            ->where('driver_id', $args['driver_id'])
            ->whereNotNull('log_id')
            ->get();

        return $liveTrips;
    }

    protected function schedule($trips, $day) 
    {
        $dateTime = date('Y-m-d', strtotime($day));
        
        foreach($trips as $trip)
            $trip->starts_at = $dateTime.' '.$trip->days[$day];
        
        return $trips->sortBy('starts_at');
    }
    
}
