<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarMake extends Model
{   
    protected $guarded = [];

    public function models()
    {
        return $this->hasMany(CarModel::class, 'make_id');
    }
}
