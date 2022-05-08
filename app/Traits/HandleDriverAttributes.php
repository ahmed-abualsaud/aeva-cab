<?php

namespace App\Traits;

use App\Driver;
trait HandleDriverAttributes
{
    protected function updateDriverStatus($driver_id, $status)
    {
       return Driver::where('id', $driver_id)->update(['cab_status' => $status]);
    }

    protected function driversToken($drivers_ids)
    {
        if (is_array($drivers_ids)) {
            return Driver::select('device_id')
                ->whereIn('id', $drivers_ids)
                ->pluck('device_id')
                ->toArray();
        }

        return Driver::select('device_id')
            ->where('id', $drivers_ids)
            ->first()->device_id;
    }
}