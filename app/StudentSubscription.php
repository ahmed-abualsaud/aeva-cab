<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentSubscription extends Model
{
    protected $guarded = [];

    public $table = 'business_trip_students';

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function scopeWhereNotScheduled($query, $trip_id)
    {
        return $query->where('trip_id', $trip_id)
        ->where('days->'.strtolower(date('l')), false);
    }
}
