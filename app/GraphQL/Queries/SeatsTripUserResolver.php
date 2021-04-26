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
            'users.name', 'users.phone', 'booking.id as booking_id', 'booking.payable', 'booking.boarding_pass'
            )
            ->join('seats_trip_bookings as booking', 'users.id', '=', 'booking.user_id')
            ->where('trip_id', $args['trip_id'])
            ->where('date', date('Y-m-d'))
            ->where('status', 'CONFIRMED');

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
