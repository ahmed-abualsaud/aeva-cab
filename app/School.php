<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $guarded = [];

    public function zone()
    {
        return $this->belongsTo(SchoolZone::class);
    }

    public function grades()
    {
        return $this->hasMany(SchoolGrade::class);
    }
}
