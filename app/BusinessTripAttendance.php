<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessTripAttendance extends Model
{
    protected $guarded = [];

    public $table = 'business_trip_attendance';

    public function scopeWhereAbsent($query, $trip_id)
    {
        return $query->select('user_id')
            ->where('trip_id', $trip_id)
            ->where('date', date("Y-m-d"))
            ->where('status', false)
            ->pluck('user_id')
            ->toArray();
    }
}
