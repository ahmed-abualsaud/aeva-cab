<?php

namespace App\GraphQL\Mutations;

use App\SeatsTrip;
use App\SeatsTripBooking;
use Illuminate\Support\Str;
use App\Exceptions\CustomException;

class SeatsTripBookingResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function createBooking($_, array $args)
    {
        try {
            if ($args['bookable'])
                $this->checkSeats($args);

            $booking = $this->saveBooking($args);

        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }

        return $booking;
    }

    public function updateBooking($_, array $args)
    {
        try {
            $input = collect($args)->except(['id','directive'])->toArray();

            $booking = SeatsTripBooking::findOrFail($args['id']);

            $booking->update($input);

        } catch (\Exception $e) {
            throw new CustomException('We could not able to update this booking!');
        }

        return $booking;
    }

    public function destroyBooking($_, array $args)
    {
        return SeatsTripBooking::whereIn('id', $args['id'])->delete();
    }

    protected function checkSeats(array $args)
    {
        $bookedSeats = SeatsTripBooking::where('trip_id', $args['trip_id'])
            ->where('trip_time', $args['trip_time'])
            ->where('status', 'CONFIRMED')
            ->sum('seats');
        
        $totalSeats = SeatsTrip::select('seats')
            ->join('vehicles', 'vehicles.id', '=', 'seats_trips.vehicle_id')
            ->where('seats_trips.id', $args['trip_id'])
            ->first()
            ->seats;

        $availableSeats = $totalSeats - $bookedSeats;

        if (!$availableSeats)
            throw new \Exception('No available seats');
            
        else if ($availableSeats < $args['seats'])
            throw new \Exception('There are only '.$availableSeats.' '. Str::plural('seat', $availableSeats));
    }

    protected function saveBooking(array $args)
    {
        try {
            $input = collect($args)->except(['directive', 'bookable'])->toArray();
            return SeatsTripBooking::create($input);
        } catch (\Exception $e) {
            throw new \Exception('You already have a trip at this time!');
        }
    }
}
