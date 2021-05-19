<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $guarded = [];

    public function schools()
    {
        return $this->hasMany(School::class, 'zone_id')
            ->where('type', 'TOSCHOOL');
    }

    public function workplaces()
    {
        return $this->hasMany(Workplace::class, 'zone_id')
            ->where('type', 'TOWORK');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
