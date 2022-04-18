<?php

namespace Qruz\Seats\Domain\Repository\Eloquent\Mutations;

use Carbon\Carbon;

use App\User;
use App\Exceptions\CustomException;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use Qruz\Seats\Domain\Models\SeatsTrip;
use Qruz\Seats\Domain\Models\SeatsTripBooking;
use Qruz\Seats\Domain\Models\SeatsTripAppTransaction;
use Qruz\Seats\Domain\Repository\Eloquent\BaseRepository;

class SeatsTripBookingRepository extends BaseRepository
{
    public function __construct(SeatsTripBooking $model)
    {
        parent::__construct($model);
    }

    public function create(array $args)
    {
        DB::beginTransaction();
        try {
            if ($this->model->where('user_id', $args['user_id'])
            ->where('trip_time', '>=', $args['trip_time'])
            ->where('status', 'Confirmed')->first()) {
                throw new CustomException(__('lang.you_already_booked_the_trip'));
            }

            $booking = $this->saveBooking($args);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            if ($args['payment_method'] === 'Card')
                $this->updateWallet($args['user_id'], -abs($args['payable']));
            throw new CustomException($e->getMessage());
        }

        return $booking;
    }

    public function update(array $args)
    {
        try {
            $input = collect($args)->except(['id','directive'])->toArray();

            $booking = $this->model->findOrFail($args['id']);

            if (array_key_exists('status', $args) && $booking->status == 'Confirmed') {
                switch($args['status']) {
                    case 'Missed':
                        $this->userMissed($booking);
                    break;
                    case 'Cancelled':
                        $this->userCancelled($booking);
                    break;
                }
            }

            $booking->update($input);

        } catch (\Exception $e) {
            throw new CustomException(__('lang.update_booking_failed'));
        }

        return $booking;
    }

    public function destroy(array $args)
    {
        return $this->model->whereIn('id', $args['id'])->delete();
    }

    protected function userMissed($booking)
    {
        if ($booking->paid)
            $this->userDidPayAndMissed($booking);
        else
            $this->updateWallet($booking->user_id, $booking->payable);
    }

    protected function userCancelled($booking)
    {
        $timeout = Carbon::parse(now())->diffInMinutes($booking->pickup_time, false) < 10;

        if ($booking->paid)
            $this->userDidPayAndCancelled($booking, $timeout);
        else 
            if ($timeout)
                $this->updateWallet($booking->user_id, $booking->payable);
    }

    protected function userDidPayAndCancelled($booking, $timeout)
    {
        if ($booking->paid < $booking->payable) {
            if ($timeout) {
                $diff = $booking->payable - $booking->paid;
                $this->updateWallet($booking->user_id, $diff);
            } else {
                $this->updateWallet($booking->user_id, -abs($booking->paid));
                $this->cancelTransaction($booking);
            }
        
        } else if ($booking->paid > $booking->payable) {
            if (!$timeout) {
                $amount = $booking->paid - $booking->payable;
                $this->updateWallet($booking->user_id, -abs($booking->payable));
                $this->updateTransaction($booking, $amount);
            }
            
        } else {
            if (!$timeout) {
                $this->updateWallet($booking->user_id, -abs($booking->paid));
                $this->cancelTransaction($booking);
            }
            
        }
    }

    protected function userDidPayAndMissed($booking)
    {
        if ($booking->paid < $booking->payable) {
            $diff = $booking->payable - $booking->paid;
            $this->updateWallet($booking->user_id, $diff);
        } 
    }

    protected function cancelTransaction($booking)
    {
        SeatsTripAppTransaction::where('booking_id', $booking->id)
            ->delete();
    }

    protected function updateTransaction($booking, $amount)
    {
        SeatsTripAppTransaction::where('booking_id', $booking->id)
            ->update(['amount' => $amount]);
    }

    protected function checkSeats(array $args)
    {
        $bookedSeats = $this->model->where('trip_id', $args['trip_id'])
            ->where('trip_time', $args['trip_time'])
            ->where('status', 'Confirmed')
            ->sum('seats');
        
        $totalSeats = SeatsTrip::select('seats')
            ->join('vehicles', 'vehicles.id', '=', 'seats_trips.vehicle_id')
            ->where('seats_trips.id', $args['trip_id'])
            ->first()
            ->seats;

        $available_seats = $totalSeats - $bookedSeats;

        if (!$available_seats)
            throw new \Exception(__('lang.no_seats'));
            
        else if ($available_seats < $args['seats'])
            throw new \Exception( __('lang.available_seats', [
                'available_seats' => $available_seats,
                'pluralSeats' => Str::plural('seat', $available_seats)
            ]));
    }

    protected function saveBooking(array $args)
    {
        switch($args['payment_method']) {
            case 'Cash':
                return $this->cashPay($args);

            case 'Card':
                return $this->cardPay($args);
        }
    }

    protected function confirmBookingAndCreateTransaction($args)
    {
        $booking = $this->confirmBooking($args);

        $this->createTransaction($args, $booking);

        return $booking;
    }

    protected function confirmBooking($args)
    {
        try {
            $input = collect($args)->except(['directive', 'bookable', 'wallet', 'trx_id'])->toArray();
            $input['boarding_pass'] = $this->createBoardingPass($input);
            return $this->model->create($input);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.create_booking_failed'));
        }
    }

    protected function createTransaction(array $args, $booking)
    {
        try {
            $input = collect($args)
                ->only(['trx_id', 'user_id', 'trip_id', 'trip_time', 'payment_method'])
                ->toArray();

            $input['booking_id'] = $booking->id;
            $input['created_by'] = 'User';
            $input['amount'] = $args['paid'];

            return SeatsTripAppTransaction::create($input);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.create_trnx_failed'));
        }
    }

    protected function createBoardingPass(array $input)
    {
        return $this->model->where('trip_id', $input['trip_id'])
            ->where('trip_time', $input['trip_time'])
            ->max('boarding_pass') + 1;
    }

    protected function cashPay($args)
    {        
        if ($args['paid']) {

            $this->updateWallet($args['user_id'], $args['paid']);

            return $this->confirmBookingAndCreateTransaction($args);
        } else {
            return $this->confirmBooking($args);
        }

    }

    protected function cardPay($args)
    {
        $extra = $args['payable'] - $args['paid'];
        
        if ($extra)
            $this->updateWallet($args['user_id'], $extra);

        return $this->confirmBookingAndCreateTransaction($args);
    }

    protected function updateWallet($user_id, $amount)
    {
        try {
            User::updateWallet($user_id, $amount);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.update_wallet_failed'));
        }
    }
}
