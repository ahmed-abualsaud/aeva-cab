<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentSubscription extends Model
{
    protected $guarded = [];

    public $table = 'business_trip_students';
}
