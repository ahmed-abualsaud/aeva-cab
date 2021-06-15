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
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            // if ($args['bookable'])
                // $this->checkSeats($args);

            $booking = $this->saveBooking($args);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            if ($args['payment_method'] === 'CARD')
                User::updateBalance($args['user_id'], -abs($args['payable']));
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
                        $this->userMissed($booking);
                    break;
                    case 'CANCELLED':
                        $this->userCancelled($booking);
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

    protected function userMissed($booking)
    {
        if ($booking->paid)
            $this->userDidPayAndMissed($booking);
        else
            User::updateBalance($booking->user_id, $booking->payable);
    }

    protected function userCancelled($booking)
    {
        $timeout = Carbon::parse(now())->diffInMinutes($booking->pickup_time, false) < 10;

        if ($booking->paid)
            $this->userDidPayAndCancelled($booking, $timeout);
        else 
            if ($timeout)
                User::updateBalance($booking->user_id, $booking->payable);
    }

    protected function userDidPayAndCancelled($booking, $timeout)
    {
        if ($booking->paid == $booking->payable) {
            if (!$timeout) {
                User::updateBalance($booking->user_id, -abs($booking->paid));
                $this->cancelTransaction($booking);
            }
            
        } else if ($booking->paid < $booking->payable) {
            if ($timeout) {
                $diff = $booking->payable - $booking->paid;
                User::updateBalance($booking->user_id, $diff); 
            } else {
                User::updateBalance($booking->user_id, -abs($booking->paid));
                $this->cancelTransaction($booking);
            }
            
        } 
    }

    protected function userDidPayAndMissed($booking)
    {
        if ($booking->paid < $booking->payable) {
            $diff = $booking->payable - $booking->paid;
            User::updateBalance($booking->user_id, $diff); 
        } 
    }

    protected function cancelTransaction($booking)
    {
        SeatsTripTransaction::where('booking_id', $booking->id)
            ->delete();
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
            throw new \Exception(
                'Only '.$availableSeats.' '.Str::plural('seat', $availableSeats).' available'
            );
    }

    protected function saveBooking(array $args)
    {
        switch($args['payment_method']) {
            case 'CASH':
                if ($args['paid'])
                    return $this->confirmBookingAndCreateTransaction($args);
            break;
            
            case 'CARD':
                return $this->confirmBookingAndCreateTransaction($args);
        }

        return $this->confirmBooking($args);
    }

    protected function confirmBookingAndCreateTransaction($args)
    {
        $booking = $this->confirmBooking($args);
        $this->createTransaction($args, $booking);
        return $booking;
    }

    protected function confirmBooking($args)
    {
        // try {
        //     SeatsTripBooking::where('user_id', $args['user_id'])
        //         ->where('pickup_time', $args['pickup_time'])
        //         ->where('status', 'CONFIRMED')
        //         ->firstOrFail();

        //         throw new \Exception('You have already booked this trip!');

        // } catch (ModelNotFoundException $e) {
            $input = collect($args)->except(['directive', 'bookable', 'wallet', 'trx_id'])->toArray();
            $input['boarding_pass'] = $this->createBoardingPass($input);
            return SeatsTripBooking::create($input);
        // }
    }

    protected function createTransaction(array $args, $booking)
    {
        try {
            $input = collect($args)
                ->only(['trx_id', 'user_id', 'trip_id', 'trip_time', 'payment_method', 'paid'])
                ->toArray();

            $input['booking_id'] = $booking->id;
            $input['created_by'] = 'USER';

            User::updateBalance($input['user_id'], $input['paid']);

            return SeatsTripTransaction::create($input);
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    protected function createBoardingPass(array $input)
    {
        return SeatsTripBooking::where('trip_id', $input['trip_id'])
            ->where('trip_time', $input['trip_time'])
            ->max('boarding_pass') + 1;
    }
}
