<?php

namespace App\GraphQL\Mutations;

use App\SeatsBooking;
use App\SeatsTripTransaction;
use App\Exceptions\CustomException;

class SeatsResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */

    public function createBooking($_, array $args)
    {
        try {
            $input = collect($args)->except(['directive'])->toArray();

            $seatsBooking = SeatsBooking::create($input);
        } catch (\Exception $e) {
            throw new CustomException('You already have a trip at this time!');
        }

        return $seatsBooking;
    }

    public function updateBooking($_, array $args)
    {
        try {
            $input = collect($args)->except(['id','directive'])->toArray();

            $seatsBooking = SeatsBooking::findOrFail($args['id']);

            $seatsBooking->update($input);

        } catch (\Exception $e) {
            throw new CustomException('We could not able to update this booking!');
        }

        return $seatsBooking;
    }

    public function createTransaction($_, array $args)
    {
        $input = collect($args)->except(['directive'])->toArray();
        return SeatsTripTransaction::create($input);
    }

    public function destroyTransaction($_, array $args)
    {
        return SeatsTripTransaction::whereIn('id', $args['id'])->delete();
    }
}
