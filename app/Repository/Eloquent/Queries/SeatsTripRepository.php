<?php

namespace App\Repository\Eloquent\Queries;

use App\SeatsTrip;
use App\Repository\Queries\SeatsTripRepositoryInterface;
use App\Repository\Eloquent\BaseRepository;

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
            ->where('status', 'CONFIRMED')
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

    protected function schedule($trips, $day) 
    {
        $dateTime = date('Y-m-d', strtotime($day));
        
        foreach($trips as $trip)
            $trip->starts_at = $dateTime.' '.$trip->days[$day];
        
        return $trips->sortBy('starts_at')->values();
    }
}
