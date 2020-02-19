<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use SoftDeletes;
    
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
