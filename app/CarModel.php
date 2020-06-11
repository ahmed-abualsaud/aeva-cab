<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarModel extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];

    public function type()
    {
        return $this->belongsTo(CarType::class);
    }

    public function make()
    {
        return $this->belongsTo(CarMake::class);
    }
}
