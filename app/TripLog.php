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
            ->select(DB::raw('log_id, DATE(created_at) as date'));

        if (array_key_exists('period', $args) && $args['period']) {
            $logHistory = $this->dateFilter($args['period'], $logHistory, 'created_at');
        }

        if (array_key_exists('user_id', $args) && $args['user_id']) {
            $logHistory = $logHistory->where('user_id', $args['user_id']);
        }

        $logHistory = $logHistory->where('trip_id', $args['trip_id'])
            ->groupBy(DB::raw('log_id, DATE(created_at)'))
            ->orderBy('date', 'desc');
         
        return $logHistory;
    }
}
