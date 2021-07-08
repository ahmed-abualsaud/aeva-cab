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
        $users = User::select(
            'users.id', 'users.name', 'users.phone', 'users.avatar', 'users.wallet_balance', 
            'booking.id as booking_id', 'booking.payable', 'booking.paid', 'booking.boarding_pass', 'booking.seats'
            )
            ->join('seats_trip_bookings as booking', 'users.id', '=', 'booking.user_id')
            ->where('trip_id', $args['trip_id'])
            ->where('trip_time', $args['trip_time'])
            ->where('status', 'CONFIRMED');

        $users = $this->usersByStatus($args, $users);

        return $users->get();
    }

    protected function usersByStatus($args, $users)
    {
        switch($args['status']) {
            case 'PICK_UP':
                $users = $users->where('booking.is_picked_up', false);
                if (array_key_exists('station_id', $args) && $args['station_id'])
                    $users = $users->where('booking.pickup_id', $args['station_id']);

            break;
            case 'DROP_OFF':
                $users = $users->where('booking.is_picked_up', true);
                if (array_key_exists('station_id', $args) && $args['station_id'])
                    $users = $users->where('booking.dropoff_id', $args['station_id']);
            break;
            default:
                $users = $users;
        }

        return $users;
    }
}
