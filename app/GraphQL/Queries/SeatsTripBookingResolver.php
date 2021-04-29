<?php

namespace App\GraphQL\Queries;

use App\PromoCode;
use App\SeatsLineStation;

class SeatsTripBookingResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function pre($_, array $args)
    {
        $wallet = auth('user')->user()->wallet_balance;

        $pickup = SeatsLineStation::select('latitude', 'longitude')
            ->find($args['pickup_id']);

        $dropoff = SeatsLineStation::select('latitude', 'longitude')
            ->find($args['dropoff_id']);

        return [
            'wallet' => $wallet,
            'pickup' => $pickup,
            'dropoff' => $dropoff
        ];
    }
}
