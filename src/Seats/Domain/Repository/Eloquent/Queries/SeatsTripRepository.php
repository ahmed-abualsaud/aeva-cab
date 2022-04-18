<?php

namespace Qruz\Seats\Domain\Repository\Eloquent\Queries;

use Qruz\Seats\Domain\Models\SeatsTrip;
use Qruz\Seats\Domain\Repository\Queries\SeatsTripRepositoryInterface;
use Qruz\Seats\Domain\Repository\Eloquent\BaseRepository;

class SeatsTripRepository extends BaseRepository implements SeatsTripRepositoryInterface
{

    public function __construct(SeatsTrip $model)
    {
        parent::__construct($model);
    }

    public function userLiveTrips(array $args)
    {
        return $this->model->join('seats_trip_bookings as b', 'b.trip_id', '=', 'seats_trips.id')
            ->where('user_id', $args['user_id'])
            ->whereNotNull('log_id')
            ->where('status', 'Confirmed')
            ->get();
    }
    public function driverTrips(array $args)
    {
        $driverTrips = $this->model->select('id', 'name', 'name_ar', 'days', 'line_id')
            ->where('driver_id', $args['driver_id'])
            ->whereRaw('? between start_date and end_date', [date('Y-m-d')])
            ->whereRaw('JSON_EXTRACT(days, "$.'.$args['day'].'") <> CAST("null" AS JSON)')
            ->get();

        if ($driverTrips->isEmpty()) return [];

        return $this->schedule($driverTrips, $args['day']);
    }
    public function driverLiveTrips(array $args)
    {
        $liveTrips = $this->model->select('id', 'name', 'name_ar')
            ->where('driver_id', $args['driver_id'])
            ->whereNotNull('log_id')
            ->get();

        return $liveTrips;
    }

    public function seatsLineStationsTrips(array $args)
    {
        $date = date('Y-m-d', strtotime($args['day']));

        $results = SeatsTrip::selectRaw('
            seats_trips.id as trip_id,
            seats_trips.base_price,
            seats_trips.minimum_distance,
            seats_trips.distance_price,
            seats_trips.bookable,
            seats_trips.ac,
            CONCAT(?, " ", JSON_UNQUOTE(JSON_EXTRACT(days, "$.'.$args['day'].'"))) as trip_time,
            ADDDATE(
                CONCAT(?, " ", JSON_UNQUOTE(JSON_EXTRACT(days, "$.'.$args['day'].'"))), 
                INTERVAL pickup.duration SECOND
            ) as pickup_time,
            (dropoff.distance - pickup.distance) as pickup_dropoff_distance
            ', [$date, $date]
        )
        ->join('seats_line_stations as pickup', 'pickup.line_id', '=', 'seats_trips.line_id')
        ->join('seats_line_stations as dropoff', 'dropoff.line_id', '=', 'seats_trips.line_id')
        ->where('seats_trips.line_id', $args['line_id'])
        ->where('pickup.id', $args['pickup_id'])
        ->where('dropoff.id', $args['dropoff_id'])
        ->whereRaw('
            JSON_EXTRACT(seats_trips.days, "$.'.$args['day'].'") <> CAST("null" AS JSON)
            and pickup.`order` < dropoff.`order`
        ')
        ->havingRaw('pickup_time > ?', [date("Y-m-d H:i:s")])
        ->oldest('pickup_time')
        ->get();

        return $results;
    }

    protected function schedule($trips, $day) 
    {
        $dateTime = date('Y-m-d', strtotime($day));
        
        foreach($trips as $trip)
            $trip->starts_at = $dateTime.' '.$trip->days[$day];
        
        return $trips->sortBy('starts_at')->values();
    }
}
