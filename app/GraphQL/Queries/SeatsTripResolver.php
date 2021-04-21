<?php

namespace App\GraphQL\Queries;

use App\SeatsTrip;
use App\Traits\Filterable;
use App\SeatsTripTransaction;
use Illuminate\Support\Facades\Cache;

class SeatsTripResolver
{
    use Filterable;

    public function nearbyStations($_, array $args)
    {
        $date = date('Y-m-d', strtotime($args['day']));

        $cacheKey = md5(implode(',', $args));

        $stations = Cache::tags('seatsNearbyStations')->remember($cacheKey, 900, function() use ($args, $date) {
            return  SeatsTrip::selectRaw('
                seats_trips.id as trip_id,
                seats_trips.price,
                seats_trips.bookable,
                partners.name as partner_name,
                pickup.id as pickup_id,
                pickup.name as pickup_name,
                dropoff.id as dropoff_id,
                dropoff.name as dropoff_name,

                CONCAT(?, " ", JSON_UNQUOTE(JSON_EXTRACT(days, "$.'.$args['day'].'"))) as trip_time,

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
            ', [$date, $date, $date, $args['plng'], $args['plat'], $args['dlng'], $args['dlat']])

            ->join('seats_trip_stations as pickup', 'seats_trips.id', '=', 'pickup.trip_id')
            ->join('seats_trip_stations as dropoff', 'seats_trips.id', '=', 'dropoff.trip_id')
            ->join('partners', 'partners.id', '=', 'seats_trips.partner_id')

            ->whereRaw('
                JSON_EXTRACT(seats_trips.days, "$.'.$args['day'].'") <> CAST("null" AS JSON)
                and pickup.`order` < dropoff.`order`
            ')
            
            ->havingRaw('
                pickup_distance < ? and
                dropoff_distance < ? and
                pickup_time > ?
            ', [2000, 2000, date("Y-m-d H:i:s")])

            ->orderBy('pickup_time')
            
            ->limit(10)
            
            ->get();
        });

        return $stations;
    }

    public function stats($_, array $args)
    {
        $transactions = SeatsTripTransaction::query();

        $transactionGroup = SeatsTripTransaction::selectRaw('
            DATE_FORMAT(created_at, "%a, %b %d, %Y") as date,
            sum(amount) as sum
        ');

        if (array_key_exists('period', $args) && $args['period']) {
            $transactions = $this->dateFilter($args['period'], $transactions, 'created_at');
            $transactionGroup = $this->dateFilter($args['period'], $transactionGroup, 'created_at');
        }

        $transactionCount = $transactions->count();
        $transactionSum = $transactions->sum('amount');
        $transactionAvg = $transactions->avg('amount');
        $transactionGroup = $transactionGroup->groupBy('date')->get();

        $response = [
            "count" => $transactionCount,
            "sum" => $transactionSum,
            "avg" => $transactionAvg,
            "transactions" => $transactionGroup
        ];

        return $response;
    }
}
