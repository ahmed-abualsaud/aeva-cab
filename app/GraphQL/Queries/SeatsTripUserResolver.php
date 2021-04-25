<?php

namespace App\GraphQL\Queries;

use App\User;

class SeatsTripUserResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $bookings = User::select(
            'users.id', 'users.name', 'users.phone', 'seats_trip_bookings.payable'
            )
            ->join('seats_trip_bookings', 'users.id', '=', 'seats_trip_bookings.user_id')
            ->where('trip_id', $args['trip_id'])
            ->where('date', date('Y-m-d'));

            switch($args['status']) {
                case 'PICK_UP':
                    $bookings = $bookings->where(function ($query) use ($args) {
                        $query->where('seats_trip_bookings.is_picked_up', false)
                            ->where('seats_trip_bookings.pickup_id', $args['station_id']);
                    });
                break;
                case 'DROP_OFF':
                    $bookings = $bookings->where(function ($query) use ($args) {
                        $query->where('seats_trip_bookings.is_picked_up', true)
                            ->where('seats_trip_bookings.dropoff_id', $args['station_id']);
                    });
                break;
                default:
                    $bookings = $bookings;
            }

            return $bookings->get();
    }
}
