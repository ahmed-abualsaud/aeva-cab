<?php

namespace App\GraphQL\Queries;

use App\BusinessTrip;

class SeatsResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */

    public function nearestStations($_, array $args)
    {
        $date = date('Y-m-d', strtotime($args['day']));

        $stations = BusinessTrip::selectRaw('
            business_trips.id as trip_id,
            business_trips.price as price,
            pickup.id as pickup_id,
            pickup.name as pickup_name,
            dropoff.id as dropoff_id,
            dropoff.name as dropoff_name,

            ADDDATE(
                CONCAT(?, " ", JSON_UNQUOTE(JSON_EXTRACT(days, "$.'.$args['day'].'"))), 
                INTERVAL '.'pickup.duration'.' SECOND
            ) as pickup_time,
            ADDDATE(
                CONCAT(?, " ", JSON_UNQUOTE(JSON_EXTRACT(days, "$.'.$args['day'].'"))), 
                INTERVAL '.'dropoff.duration'.' SECOND
            ) as dropoff_time,

            ST_Distance_Sphere(point(pickup.longitude, pickup.latitude), point(?, ?)
            ) AS pickup_distance,
            ST_Distance_Sphere(point(dropoff.longitude, dropoff.latitude), point(?, ?)
            ) AS dropoff_distance
        ', [$date, $date, $args['plng'], $args['plat'], $args['dlng'], $args['dlat']])

        ->join('business_trip_stations as pickup', 'business_trips.id', '=', 'pickup.trip_id')
        ->join('business_trip_stations as dropoff', 'business_trips.id', '=', 'dropoff.trip_id')

        ->whereRaw('
            JSON_EXTRACT(business_trips.days, "$.'.$args['day'].'") <> CAST("null" AS JSON)
            and pickup.`order` < dropoff.`order`
        ')
        
        ->havingRaw('
            pickup_distance < ? and
            dropoff_distance < ? and
            pickup_time > ?
        ', [2500, 2500, date("Y-m-d H:i:s")])

        ->orderBy('pickup_time')
        
        ->limit(10)
        
        ->get();

        return $stations;
    }
}
