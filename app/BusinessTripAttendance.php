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
            ->where('date', date('Y-m-d'))
            ->where('is_absent', true)
            ->pluck('user_id')
            ->toArray();
    }

    public function scopeWhereAbsentStudents($query, $trip_id)
    {
        return $query->select('students')
            ->whereNotNull('students')
            ->where('trip_id', $trip_id)
            ->where('date', date('Y-m-d'))
            ->where('is_absent', true)
            ->pluck('students')
            ->flatten()
            ->toArray();
    }

    public function scopeWhereStudents($query, $args)
    {
        return $query->select('students')
            ->whereNotNull('students')
            ->where('trip_id', $args['trip_id'])
            ->where('user_id', $args['user_id'])
            ->where('date', $args['date'])
            ->pluck('students')
            ->first();
    }

    public function getStudentsAttribute($value) {
        return json_decode($value);
    }
}
