<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessTripChat extends Model
{
    protected $guarded = [];

    public $table = 'business_trip_chat';

    public function sender()
    {
        return $this->morphTo();
    }
}
