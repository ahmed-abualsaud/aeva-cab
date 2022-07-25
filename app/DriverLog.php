<?php

namespace App;

use App\Traits\Filterable;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;

class DriverLog extends Model
{
    use Filterable;

    protected $guarded = [];

    protected $appends = [
        'acceptance_rate', 
        'cancellation_rate',
        'missing_rate'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function scopeLogs($query, array $args) 
    {
        if (array_key_exists('driver_id', $args)) {
            $query = $query->where('driver_id', $args['driver_id']);
        }

        if (array_key_exists('period', $args) && $args['period']) {
            $query = $this->dateFilter($args['period'], $query, 'created_at');
        }

        return $query;
    }

    public function scopeSummary($query, array $args) 
    {
        $query = $this->scopeLogs($query, $args);

        return $query->selectRaw('
            sum(cash) as cash,
            sum(wallet) wallet,
            sum(earnings) as earnings,
            sum(received_cab_requests) as received_cab_requests,
            sum(accepted_cab_requests) as accepted_cab_requests,
            sum(cancelled_cab_requests) as cancelled_cab_requests,
            sum(missed_cab_requests) as missed_cab_requests,
            sum(total_working_time) as total_working_time
        ')
        ->groupBy('driver_id');
    }

    public static function log(array $args)
    {
        if (!is_array($args['driver_id'])) {
            $args['driver_id'] = [$args['driver_id']];
        }

        foreach ($args['driver_id'] as $driver_id) 
        {
            $last_log = DriverLog::where('driver_id', $driver_id)->latest()->first();
            $inputs = Arr::except($args, ['driver_id', 'activity_updated_at']);

            if(!$last_log || (time() - strtotime(substr($last_log->created_at, 0, 10))) >= 86400) {
                $inputs['driver_id'] = $driver_id;
                $last_log = DriverLog::create($inputs);
            } else {
                $inc_keys = [
                    'cash',
                    'wallet',
                    'earnings',
                    'received_cab_requests',
                    'accepted_cab_requests',
                    'cancelled_cab_requests',
                    'missed_cab_requests',
                    'total_working_time'
                ];

                foreach ($inputs as $key => $value) {
                    if (in_array($key, $inc_keys)) {
                        $inputs[$key] = $last_log->{$key} + $value;
                    }
                }

                $last_log->update($inputs);
            }
            $arr[] = $last_log;
        }

        return $arr;
    }

    public function getAcceptanceRateAttribute()
    {
        if ($this->received_cab_requests == 0) {return 0;}
        return ($this->accepted_cab_requests / $this->received_cab_requests);
    }

    public function getCancellationRateAttribute()
    {
        if ($this->accepted_cab_requests == 0) {return 0;}
        return ($this->cancelled_cab_requests / $this->accepted_cab_requests);
    }

    public function getMissingRateAttribute()
    {
        if ($this->received_cab_requests == 0) {return 0;}
        return ($this->missed_cab_requests / $this->received_cab_requests);
    }
}
