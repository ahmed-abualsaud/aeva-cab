<?php

namespace App\Repository\Eloquent;

use App\User;
use App\Repository\BusinessTripAttendanceRepositoryInterface;
use Illuminate\Support\Collection;

class BusinessTripAttendanceRepository extends BaseRepository implements BusinessTripAttendanceRepositoryInterface
{

   /**
    * BusinessTripAttendanceRepository constructor.
    *
    * @param User $model
    */
   public function __construct(User $model)
   {
        parent::__construct($model);
   }

   /**
    * @return Collection
    */
   public function get(array $args): Collection
   {
        $users = $this->model->select('users.id', 'users.name', 'users.phone', 'users.secondary_no', 'users.avatar')
        ->join('business_trip_users', 'users.id', '=', 'business_trip_users.user_id')
        ->where('business_trip_users.trip_id', $args['trip_id']);

        if (array_key_exists('date', $args) && $args['date']) {
            $users = $users->leftJoin('business_trip_attendance', function ($join) use ($args) {
                $join->on('business_trip_attendance.user_id', '=', 'users.id')
                    ->where('business_trip_attendance.trip_id', $args['trip_id'])
                    ->where('business_trip_attendance.date', $args['date']);
                })
                ->addSelect('business_trip_attendance.is_absent', 'business_trip_attendance.comment');
        } else {
            $users = $users->where('business_trip_users.is_scheduled', true)
                ->where('business_trip_users.is_picked_up', false)
                ->whereNotNull('business_trip_users.subscription_verified_at')
                ->addSelect('business_trip_users.is_absent');
        }

        return $users->get();
   }
}