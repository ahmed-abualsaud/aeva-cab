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
            'users.name', 'users.phone', 'users.wallet_balance', 
            'booking.id as booking_id', 'booking.payable', 'booking.boarding_pass'
            )
            ->join('seats_trip_bookings as booking', 'users.id', '=', 'booking.user_id')
            ->where('trip_id', $args['trip_id'])
            ->where('date', date('Y-m-d'))
            ->where('status', 'CONFIRMED');

            switch($args['status']) {
                case 'PICK_UP':
                    $bookings = $bookings->where('booking.is_picked_up', false);
                    if (array_key_exists('station_id', $args) && $args['station_id'])
                        $bookings = $bookings->where('booking.pickup_id', $args['station_id']);

                break;
                case 'DROP_OFF':
                    $bookings = $bookings->where('booking.is_picked_up', true);
                    if (array_key_exists('station_id', $args) && $args['station_id'])
                        $bookings = $bookings->where('booking.dropoff_id', $args['station_id']);
                break;
                default:
                    $bookings = $bookings;
            }

            return $bookings->get();
    }
}
