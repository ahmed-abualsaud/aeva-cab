<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarMake extends Model
{   
    use SoftDeletes;

    protected $guarded = [];

    public function models()
    {
        return $this->hasMany(CarModel::class, 'make_id');
    }
}
