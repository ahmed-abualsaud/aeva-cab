<?php

namespace App\Traits;

use App\Driver;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HandleDriverAttributes
{
    protected function updateDriverStatus($driver_id, $cab_status)
    {
        try {
            $driver = Driver::findOrFail($driver_id);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.request_not_found'));
        }

        if (strtolower($cab_status) == 'riding') {
            return $driver->update([
                'cab_status' => $cab_status
            ]);
        }

        $activity_updated_at = date('Y-m-d H:i:s');

        if (strtolower($cab_status) == 'offline' && $driver->cab_status == 'Online') {
            $total_working_time = strtotime($activity_updated_at) - strtotime($driver->activity_updated_at);
            $total_working_time = $total_working_time / 60 + $driver->total_working_time;
            return $driver->update([
                'cab_status' => $cab_status,
                'total_working_time' => $total_working_time,
                'activity_updated_at'=> $activity_updated_at
            ]);
        }

        if (strtolower($cab_status) == 'online') {
                return $driver->update([
                'cab_status' => $cab_status,
                'activity_updated_at'=> $activity_updated_at
            ]);
        }
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