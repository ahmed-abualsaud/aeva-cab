<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolZone extends Model
{
    protected $guarded = [];

    public function schools()
    {
        return $this->hasMany(School::class, 'zone_id');
    }
}
