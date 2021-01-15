<?php

namespace App\GraphQL\Queries;

use App\User;

class BusinessTripAttendanceResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $users = User::select('users.id', 'users.name', 'users.phone', 'users.avatar')
            ->join('business_trip_users', function ($join) use ($args) {
                $join->on('business_trip_users.user_id', '=', 'users.id')
                    ->where('business_trip_users.trip_id', $args['trip_id']);
            });

        if (array_key_exists('date', $args)) {
            $users = $users->leftJoin('business_trip_attendance', function ($join) use ($args) {
                $join->on('business_trip_attendance.user_id', '=', 'users.id')
                    ->where('business_trip_attendance.trip_id', $args['trip_id'])
                    ->whereDate('business_trip_attendance.date', $args['date']);
                })
                ->addSelect('business_trip_attendance.is_absent', 'business_trip_attendance.comment');
        } else {
            $users = $users->addSelect('business_trip_users.is_absent');
        }

        return $users->get();
    }
}
