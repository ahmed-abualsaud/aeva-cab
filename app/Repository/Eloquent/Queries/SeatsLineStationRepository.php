<?php

namespace App\Repository\Eloquent\Queries;

use App\SeatsTrip;
use Illuminate\Support\Facades\Cache;
use App\Repository\Queries\SeatsLineStationRepositoryInterface;

class SeatsLineStationRepository extends BaseRepository implements SeatsLineStationRepositoryInterface
{
    private $cache;

    public function __construct(SeatsTrip $model, Cache $cache)
    {
        parent::__construct($model);
        $this->cache = $cache;
    }

    public function nearby(array $args)
    {
        $date = date('Y-m-d', strtotime($args['day']));

        return $this->cache->tags('seatsNearbyStations')
            ->remember(md5(implode(',', $args)), 900, fn() =>
              $this->model->selectRaw('
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
            ->join('seats_line_stations as pickup', 'pickup.line_id', '=', 'seats_trips.line_id')
            ->join('seats_line_stations as dropoff', 'dropoff.line_id', '=', 'seats_trips.line_id')
            ->join('partners', 'partners.id', '=', 'seats_trips.partner_id')
            ->whereRaw('
                JSON_EXTRACT(seats_trips.days, "$.'.$args['day'].'") <> CAST("null" AS JSON)
                and pickup.`order` < dropoff.`order`
            ')
            ->havingRaw('
                pickup_distance < ? and
                dropoff_distance < ? and
                pickup_time > ?
            ', [4500, 4500, date("Y-m-d H:i:s")])
            ->orderBy('pickup_time')
            ->limit(10)
            ->get()
        );
    }

}
