<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverVehicle extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function scopeByDriver($query, array $args)
    {
        return $query->select('vehicle_id')
            ->where('driver_id', $args['driver_id'])
            ->pluck('vehicle_id');
    }

}
