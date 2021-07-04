<?php

namespace App\Repository\Eloquent\Queries;   

use App\User;
use App\BusinessTripSubscription;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use App\Repository\Queries\BusinessTripSubscriptionRepositoryInterface;
use Illuminate\Support\Collection;

class BusinessTripSubscriptionRepository extends BaseRepository implements BusinessTripSubscriptionRepositoryInterface
{
    private $user;

    /**
    * BusinessTripSubscriptionRepository constructor.
    *
    * @param User $model
    */
    public function __construct(BusinessTripSubscription $model, User $user)
    {
        parent::__construct($model);
        $this->user = $user;
    }
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function businessTripSubscribedUsers(array $args): Collection
    {
        $users = $this->user->selectRaw(
            'users.id, users.name, users.avatar, users.phone, 
            station.id AS station_id, station.name AS station_name, 
            destination.id AS destination_id, destination.name AS destination_name, 
            subscription.subscription_verified_at, subscription.payable, subscription.due_date'
        )
        ->join(
            'business_trip_users as subscription', 
            'subscription.user_id', '=', 'users.id'
        )
        ->leftJoin(
            'business_trip_stations as station', 
            'station.id', '=', 'subscription.station_id'
        )
        ->leftJoin(
            'business_trip_stations as destination', 
            'destination.id', '=', 'subscription.destination_id'
        )
        ->where('subscription.trip_id', $args['trip_id'])
        ->get();

        return $users;
    }

    public function businessTripStationUsers(array $args): Collection
    {
        $users = $this->user->select('users.id', 'users.name', 'users.avatar', 'users.phone')
            ->join('business_trip_users', 'business_trip_users.user_id', '=', 'users.id');

            if ($args['status'] == 'assigned') {
                $users = $users->where('station_id', $args['station_id'])
                    ->orWhere('destination_id', $args['station_id'])
                    ->addSelect(DB::raw('
                        (CASE 
                            WHEN station_id = '.$args['station_id'].' 
                            THEN "pickup" ELSE "dropoff"
                            END
                        ) AS station_type
                    '));
            } else {
                $users = $users->where('trip_id', $args['trip_id'])
                    ->where(function ($query) use ($args) {
                        $query->whereNull('station_id')
                            ->orWhere('station_id', '<>', $args['station_id']);
                });
            }

        return $users->get();
    }

    public function businessTripSubscribers(array $args): Collection
    {
        $users = $this->user->select('users.id', 'users.name', 'users.phone', 'users.secondary_no')
            ->join('business_trip_users', 'users.id', '=', 'business_trip_users.user_id')
            ->where('business_trip_users.trip_id', $args['trip_id'])
            ->where('business_trip_users.is_scheduled', true)
            ->where('business_trip_users.is_absent', false)
            ->whereNotNull('business_trip_users.subscription_verified_at');

            $users = $this->usersByStatus($args, $users);

        return $users->get();
    }

    public function businessTripUsersStatus(array $args): Collection
    {
        $users = $this->user->selectRaw('users.id, users.name, users.phone, users.secondary_no, users.avatar, business_trip_users.is_picked_up, business_trip_users.is_absent')
            ->join('business_trip_users', 'users.id', '=', 'business_trip_users.user_id');

            if (array_key_exists('trip_id', $args) && $args['trip_id']) {
                $users = $users->where('business_trip_users.trip_id', $args['trip_id']);
            }
            
            if (array_key_exists('station_id', $args) && $args['station_id']) {
                $users = $users->where('business_trip_users.station_id', $args['station_id']);
            }

        return $users->get();
    }

    public function businessTripUserStatus(array $args)
    {
        try {
            $status = $this->model->select('is_absent', 'is_picked_up')
                ->where('trip_id', $args['trip_id'])
                ->where('user_id', $args['user_id'])
                ->firstOrFail();
        } catch (\Exception $e) {
            throw new CustomException(__('lang.get_user_status_failed'));
        }

        return $status;
    }

    protected function usersByStatus($args, $users)
    {
        switch($args['status']) {
            case 'PICK_UP':
                $users = $users->where('business_trip_users.is_picked_up', false);
                if (array_key_exists('station_id', $args) && $args['station_id'])
                    $users = $users->where('station_id', $args['station_id']);

            break;
            case 'DROP_OFF':
                $users = $users->where('business_trip_users.is_picked_up', true);
                    if (array_key_exists('station_id', $args) && $args['station_id'])
                        $users = $users->where('destination_id', $args['station_id']);

            break;
            default:
                $users = $users;
        }

        return $users;
    }
}
