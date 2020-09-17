<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OndemandRequestVehicle extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function carType()
    {
        return $this->belongsTo(CarType::class, 'car_type_id');
    }

    public function carModel()
    {
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }
}
