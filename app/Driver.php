<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $guarded = [];

    public function fleet()
    {
        return $this->belongsTo(Fleet::class);
    }

    public function trips()
    {
        return $this->hasMany(PartnerTrip::class);
    }
}
