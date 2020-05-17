<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverVehicle extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function driver()
    {
        return $this->belongsTo('App\Driver', 'driver_id');
    }

    public function vehicle()
    {
        return $this->belongsTo('App\Vehicle');
    }
}
