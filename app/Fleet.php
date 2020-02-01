<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fleet extends Model
{
    protected $guarded = [];

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    public function carTypes()
    {
        return $this->belongsToMany(CarType::class, 'fleet_car_types');
    }

    public function carModels()
    {
        return $this->belongsToMany(CarModel::class, 'fleet_car_models');
    }
}
