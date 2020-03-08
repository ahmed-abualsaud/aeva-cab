<?php

namespace App;

use App\DriverVehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];

    public function make()
    {
        return $this->belongsTo(CarMake::class, 'car_make_id');
    }

    public function model()
    {
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }

    public function type()
    {
        return $this->belongsTo(CarType::class, 'car_type_id');
    }

    public function scopeAssignedOrNot($query, $args) 
    {
        $driverVehicles = DriverVehicle::where('driver_id', $args['driver_id'])->get()->pluck('vehicle_id');

        if ($args['assigned']) {
            return $query->whereIn('id', $driverVehicles);
        } else {
            return $query->whereNotIn('id', $driverVehicles);
        }
    }
}
