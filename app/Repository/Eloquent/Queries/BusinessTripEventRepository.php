<?php

namespace App\Repository\Eloquent\Queries;   

use App\Traits\Searchable;
use App\Traits\Filterable;
use App\BusinessTripEvent;
use App\Repository\Eloquent\BaseRepository;

class BusinessTripEventRepository extends BaseRepository
{
    use Searchable, Filterable;

    public function __construct(BusinessTripEvent $model)
    {
        parent::__construct($model);
    }

    public function index(array $args)
    {
        if (array_key_exists('trip_id', $args) && $args['trip_id']) {
            $events = $this->model->selectRaw('
                drivers.id AS driver_id, drivers.name AS driver_name, business_trip_events.*
            ')
            ->where('trip_id', $args['trip_id']);
        } else {
            $events = $this->model->selectRaw('
                business_trips.id AS trip_id, business_trips.name AS trip_name,
                business_trips.duration AS trip_duration, business_trips.distance AS trip_distance,
                drivers.id AS driver_id, drivers.name AS driver_name,
                business_trip_events.*
            ')
            ->join('business_trips', 'business_trips.id', '=', 'business_trip_events.trip_id');

            if (array_key_exists('partner_id', $args) && $args['partner_id']) {
                $events = $events->where('business_trips.partner_id', $args['partner_id']);
            }

            if (array_key_exists('type', $args) && $args['type']) {
                $events = $events->where('business_trips.type', $args['type']);
            }
        }

        if (array_key_exists('searchQuery', $args) && $args['searchQuery']) {
            $events = $this->search($args['searchFor'], $args['searchQuery'], $events);
        }

        if (array_key_exists('period', $args) && $args['period']) {
            $events = $this->dateFilter($args['period'], $events, 'business_trip_events.created_at');
        }

        return $events->join('drivers', 'drivers.id', '=', 'business_trip_events.driver_id')
            ->latest('business_trip_events.created_at');
    }
}