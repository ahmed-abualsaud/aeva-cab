<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $guarded = [];

    public function zones()
    {
        $this->hasMany(SchoolZone::class, 'city_id');
    }
}
