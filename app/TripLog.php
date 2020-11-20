<?php

namespace App;

use App\Traits\Filterable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;

class TripLog extends Model
{
    use Filterable;
    
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function index($_, array $args): Builder
    {
        $logHistory = DB::table('trip_logs')
            ->select(DB::raw('log_id, DATE_FORMAT(created_at, "%a, %b %d, %Y") as date'));

        if (array_key_exists('period', $args) && $args['period']) {
            $logHistory = $this->dateFilter($args['period'], $logHistory, 'created_at');
        }

        if (array_key_exists('user_id', $args) && $args['user_id']) {
            $logHistory = $logHistory->where('user_id', $args['user_id']);
        }

        $logHistory = $logHistory->where('trip_id', $args['trip_id'])
            ->groupBy(DB::raw('log_id, DATE_FORMAT(created_at, "%a, %b %d, %Y")'))
            ->orderBy('date', 'desc');
         
        return $logHistory;
    }

    public function feed($_, array $args): Builder
    {
        $feed = DB::table('trip_logs')
            ->join('business_trips', 'business_trips.id', '=', 'trip_logs.trip_id')
            ->selectRaw('
                business_trips.name AS trip_name,
                trip_logs.status, 
                trip_logs.created_at
            ')
            ->where(function ($query) {
                $query->where('trip_logs.status', 'STARTED')
                    ->orWhere('trip_logs.status', 'ENDED');
            });

            if (array_key_exists('period', $args) && $args['period']) {
                $feed = $this->dateFilter($args['period'], $feed, 'trip_logs.created_at');
            }

            $feed = $feed->latest('trip_logs.created_at');

        return $feed;
    }
}
