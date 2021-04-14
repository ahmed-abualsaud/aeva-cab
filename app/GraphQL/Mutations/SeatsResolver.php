<?php

namespace App\GraphQL\Mutations;

use App\BusinessTrip;
use App\SeatsBooking;
use Illuminate\Support\Str;
use App\SeatsTransaction;
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

            $this->checkSeats($args);

            $seatsBooking = $this->saveBooking($args);

        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
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

    public function destroyBooking($_, array $args)
    {
        return SeatsBooking::whereIn('id', $args['id'])->delete();
    }

    public function createTransaction($_, array $args)
    {
        $input = collect($args)->except(['directive'])->toArray();
        return SeatsTransaction::create($input);
    }

    public function destroyTransaction($_, array $args)
    {
        return SeatsTransaction::whereIn('id', $args['id'])->delete();
    }

    protected function checkSeats(array $args)
    {
        $bookedSeats = SeatsBooking::where('trip_id', $args['trip_id'])
            ->where('trip_time', $args['trip_time'])
            ->where('status', 'CONFIRMED')
            ->sum('seats');
        
        $totalSeats = BusinessTrip::select('seats')
            ->join('vehicles', 'vehicles.id', '=', 'business_trips.vehicle_id')
            ->where('business_trips.id', $args['trip_id'])
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
            $input = collect($args)->except(['directive'])->toArray();
            return SeatsBooking::create($input);
        } catch (\Exception $e) {
            throw new \Exception('You already have a trip at this time!');
        }
    }
}
