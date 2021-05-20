<?php

namespace App;

use App\Scopes\SortByOrderScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;

class SeatsLineStation extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SortByOrderScope);
    }

    public function nearby($_, array $args): Builder
    {
        $date = date('Y-m-d', strtotime($args['day']));

        return DB::table('seats_trips')
            ->selectRaw('
                seats_trips.id as trip_id,
                seats_trips.price,
                seats_trips.bookable,
                pickup.id as pickup_id,
                pickup.name as pickup_name,
                dropoff.id as dropoff_id,
                dropoff.name as dropoff_name,
                "Placeholder Partner" as partner_name,
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
            ', [$date, $date, $date, $args['plng'], $args['plat'], $args['dlng'], $args['dlat']]
            )
            ->join('seats_line_stations as pickup', 'pickup.line_id', '=', 'seats_trips.line_id')
            ->join('seats_line_stations as dropoff', 'dropoff.line_id', '=', 'seats_trips.line_id')
            ->whereRaw('
                JSON_EXTRACT(seats_trips.days, "$.'.$args['day'].'") <> CAST("null" AS JSON)
                and pickup.`order` < dropoff.`order`
            ')
            ->havingRaw('
                pickup_distance < ? and
                dropoff_distance < ? and
                pickup_time > ?
            ', [6500, 6500, date("Y-m-d H:i:s")])
            ->oldest('pickup_time');
    }
}
