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
        $driverTrips = SeatsTrip::where('driver_id', $args['driver_id'])
            ->whereRaw('? between start_date and end_date', [date('Y-m-d')])
            ->whereRaw('JSON_EXTRACT(days, "$.'.$args['day'].'") <> CAST("null" AS JSON)')
            ->get();

        if ($driverTrips->isEmpty()) return [];

        return $this->scheduledTrips($driverTrips, $args['day']);
    }

    public function driverLiveTrips($_, array $args)
    {
        $liveTrips = SeatsTrip::where('driver_id', $args['driver_id'])
            ->whereNotNull('log_id')
            ->get();

        return $liveTrips;
    }

    protected function scheduledTrips($trips, $day) 
    {
        $dateTime = date('Y-m-d', strtotime($day));
        
        foreach($trips as $trip) {
            $tripInstance = new SeatsTrip();
            $trip->start_time = strtotime($dateTime.' '.$trip->days[$day]) * 1000;
            $tripInstance->fill($trip->toArray());
            $sortedTrips[] = $tripInstance;
        }

        usort($sortedTrips, function ($a, $b) { return ($a['start_time'] > $b['start_time']); });
        
        return $sortedTrips;
    }
    
}
