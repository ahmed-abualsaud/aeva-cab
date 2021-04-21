<?php

namespace App\GraphQL\Mutations;

use App\SeatsTrip;
use App\SeatsTripBooking;
use App\SeatsTripStation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\SeatsTripTransaction;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class SeatsTripResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        DB::beginTransaction();
        try {
            $input = Arr::except($args, ['directive']);
            $trip = $this->createTrip($input);

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException('We could not able to create this trip!');
        }

        return $trip;
    }

    public function update($_, array $args)
    {
        try {
            $tripInput = Arr::except($args, ['directive']);
            $trip = SeatsTrip::findOrFail($args['id']);
            $trip->update($tripInput);
        } catch (ModelNotFoundException $e) {
            throw new CustomException('Trip with the provided ID is not found.');
        }

        return $trip;
    }

    public function copy($_, array $args)
    {
        DB::beginTransaction();
        try {
            $trip = $this->createTripCopy($args);

            if ($args['include_stations'])
                $this->createStationsCopy($args['id'], $trip->id);

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException('We could not able to copy this trip!');
        }

        return $trip;
    }

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

    public function createTransaction($_, array $args)
    {
        $input = collect($args)->except(['directive'])->toArray();
        return SeatsTripTransaction::create($input);
    }

    public function destroyTransaction($_, array $args)
    {
        return SeatsTripTransaction::whereIn('id', $args['id'])->delete();
    }

    public function updateRoute($_, array $args)
    {
        return SeatsTrip::reroute($args);
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

    protected function createTrip($input)
    {
        return SeatsTrip::create($input);
    }

    protected function createTripCopy(array $args)
    {
        $originalTrip = SeatsTrip::select(
            'partner_id', 'driver_id', 'vehicle_id', 'start_date', 'end_date', 
            'days', 'duration', 'distance', 'bookable', 'price', 'route'
            )
            ->findOrFail($args['id'])
            ->toArray();

        $originalTrip['name'] = $args['name'];
        
        return $this->createTrip($originalTrip);
    }

    protected function createStationsCopy($oldTripId, $newTripId)
    {
        $originalStations = SeatsTripStation::select(
            'name', 'latitude', 'longitude', 'duration', 'distance', 'state', 'order'
            )
            ->where('trip_id', $oldTripId)
            ->get();

        foreach($originalStations as $station) {
            $station->trip_id = $newTripId;
            $station->created_at = now();
            $station->updated_at = now();
        }

        return SeatsTripStation::insert($originalStations->toArray());
    }
}
