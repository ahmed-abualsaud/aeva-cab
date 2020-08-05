<?php

namespace App\GraphQL\Queries;

use App\Driver;
use App\TripLog;
use App\BusinessTripUser;
use App\Traits\DateFilter;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BusinessTripLogResolver
{
    use DateFilter;
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function businessTripLog($_, array $args)
    {
        $log = TripLog::selectRaw('trip_logs.status, trip_logs.latitude, trip_logs.longitude, trip_logs.created_at, users.name as user')
            ->leftJoin('users', 'users.id', '=', 'trip_logs.user_id')
            ->where('log_id', $args['log_id'])
            ->where('status', '<>', 'NOT_PICKED_UP')
            ->orderBy('trip_logs.created_at')
            ->get();

        return $log; 
    }

    public function businessTripLogHistory($_, array $args)
    {
        $logHistory = TripLog::selectRaw('log_id, DATE(created_at) as date');

        if (array_key_exists('period', $args) && $args['period']) {
            $logHistory = $this->dateFilter($args['period'], $logHistory, 'created_at');
        }

        $logHistory = $logHistory->where('trip_id', $args['trip_id'])
            ->groupBy('log_id','date')
            ->orderBy('date', 'desc')
            ->get();
        
        return $logHistory;
    }

    public function driverLocation($_, array $args)
    {
        try {
            $location = Driver::select(['latitude', 'longitude'])
                ->findOrFail($args['driver_id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('No data for the provided driver ID');
        }

        return [
            'latitude' => $location->latitude,
            'longitude' => $location->longitude
        ];
    }

    public function pickedUsers($_, array $args)
    {
        $users = TripLog::where('log_id', $args['log_id'])
            ->where('status', 'PICKED_UP')
            ->join('users', 'users.id', '=', 'trip_logs.user_id')
            ->select('users.*')
            ->get();

        return $users;
    }

    public function arrivedAndPickedUsers($_, array $args)
    {
        $users = BusinessTripUser::selectRaw('
            users.id, users.name, users.phone, users.avatar, 
            (CASE WHEN isPickedLog.status IS NULL THEN 0 ELSE 1 END) AS is_picked_up, 
            (CASE WHEN isArrivedLog.status IS NULL THEN 0 ELSE 1 END) AS is_arrived
        ')
            ->where('station_id', $args['station_id'])
            ->join('users', 'users.id', '=', 'business_trip_users.user_id')
            ->leftJoin(\DB::raw('(SELECT user_id, log_id, status FROM trip_logs) isPickedLog'), 
                function ($join) use ($args) {
                    $join->on('business_trip_users.user_id', '=', 'isPickedLog.user_id')
                        ->where('isPickedLog.log_id', $args['log_id'])
                        ->where('isPickedLog.status', 'PICKED_UP');
                }
            )
            ->leftJoin(\DB::raw('(SELECT user_id, log_id, status FROM trip_logs) isArrivedLog'), 
                function ($join) use ($args) {
                    $join->on('business_trip_users.user_id', '=', 'isArrivedLog.user_id')
                        ->where('isArrivedLog.log_id', $args['log_id'])
                        ->where('isArrivedLog.status', 'ARRIVED');
                }
            )
            ->get();

        return $users;
    }

    public function arrivedAndPickedUsersLite($_, array $args)
    {
        $users = BusinessTripUser::select(['users.id', 'users.name', 'users.avatar'])
            ->where('station_id', $args['station_id'])
            ->join('users', 'users.id', '=', 'business_trip_users.user_id')
            ->addSelect(['is_picked_up' => TripLog::select('status')
                ->whereColumn('user_id', 'business_trip_users.user_id')
                ->where('status', 'PICKED_UP')
            ])
            ->addSelect(['is_arrived' => TripLog::select('status')
                ->whereColumn('user_id', 'business_trip_users.user_id')
                ->where('status', 'ARRIVED')
            ])
            ->get();

        return $users;
    }

    public function arrivedAndNotArrivedUsers($_, array $args)
    {
        $users = BusinessTripUser::where('station_id', $args['station_id'])
            ->join('users', 'users.id', '=', 'business_trip_users.user_id')
            ->leftJoin('trip_logs', function ($join) use ($args) {
                $join->on('users.id', '=', 'trip_logs.user_id')
                    ->where('trip_logs.log_id', $args['log_id'])
                    ->where('status', 'ARRIVED');
            })
            ->selectRaw('users.*, (CASE WHEN trip_logs.status IS NULL THEN 0 ELSE 1 END) AS is_arrived
            ')
            ->get();

        return $users;
    }

}
