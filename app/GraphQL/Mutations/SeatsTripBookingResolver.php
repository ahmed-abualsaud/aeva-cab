<?php

namespace App\GraphQL\Mutations;

use App\User;
use App\SeatsTrip;
use Carbon\Carbon;
use App\SeatsTripBooking;
use Illuminate\Support\Str;
use App\SeatsTripTransaction;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class SeatsTripBookingResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        DB::beginTransaction();
        try {
            if ($args['bookable'])
                $this->checkSeats($args);

            $booking = $this->saveBooking($args);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new CustomException($e->getMessage());
        }

        return $booking;
    }

    public function update($_, array $args)
    {
        try {
            $input = collect($args)->except(['id','directive'])->toArray();

            $booking = SeatsTripBooking::findOrFail($args['id']);

            if (array_key_exists('status', $args) && $booking->status == 'CONFIRMED') {
                switch($args['status']) {
                    case 'MISSED':
                        User::updateBalance($booking->user_id, $booking->payable);
                    break;
                    case 'CANCELLED':
                        $this->cancelBooking($booking);
                    break;
                }
            }

            $booking->update($input);

        } catch (\Exception $e) {
            throw new CustomException('We could not able to update this booking!');
        }

        return $booking;
    }

    public function destroy($_, array $args)
    {
        return SeatsTripBooking::whereIn('id', $args['id'])->delete();
    }

    protected function cancelBooking($booking)
    {
        if (Carbon::parse(now())->diffInMinutes($booking->pickup_time, false) < 10) {
            User::updateBalance($booking->user_id, $booking->payable);
        } else if ($booking->is_paid) {
            User::updateBalance($booking->user_id, -abs($booking->payable));
            SeatsTripTransaction::where('booking_id', $booking->id)
                ->delete();
        }
    }

    protected function checkSeats(array $args)
    {
        $bookedSeats = SeatsTripBooking::where('trip_id', $args['trip_id'])
            ->where('date', $args['date'])
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
            throw new \Exception(
                'Only '.$availableSeats.' '.Str::plural('seat', $availableSeats).' available'
            );
    }

    protected function saveBooking(array $args)
    {
        $input = collect($args)->except(['directive', 'bookable'])->toArray();
        $input['boarding_pass'] = $this->createBoardingPass($args);

        if ($args['payment_method'] === 'CASH' 
            && auth('user')->user() 
            && auth('user')->user()->wallet_balance >= $args['payable']) {

            $input['is_paid'] = true;
            $booking = $this->confirmBooking($input);
            $this->createTransaction($args, $booking);

            return $booking;
        }

        return $this->confirmBooking($input);;
    }

    protected function confirmBooking($input)
    {
        try {
            return SeatsTripBooking::create($input);
        } catch (\Exception $e) {
            throw new \Exception('You already have a trip at this time!');
        }
    }

    protected function createTransaction(array $args, $booking)
    {
        try {
            $input = collect($args)
                ->only(['user_id', 'trip_id', 'payment_method'])
                ->toArray();

            $input['paid'] = $args['payable'];
            $input['booking_id'] = $booking->id;

            User::updateBalance($input['user_id'], $input['paid']);

            return SeatsTripTransaction::create($input);
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    protected function createBoardingPass(array $args)
    {
        return SeatsTripBooking::where('trip_id', $args['trip_id'])
            ->where('date', $args['date'])
            ->max('boarding_pass') + 1;
    }
}
