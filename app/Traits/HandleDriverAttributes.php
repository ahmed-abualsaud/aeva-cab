<?php

namespace App\Traits;

use App\Driver;
trait HandleDriverAttributes
{
    protected function updateDriverStatus($driver_id, $status)
    {
        Driver::where('id', $driver_id)->update(['status' => $status]);
    }

    protected function driversToken(array $drivers_ids)
    {
        return Driver::select('device_id')
            ->whereIn('id', $drivers_ids)
            ->pluck('device_id')
            ->toArray();
    }
}