<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CabRequest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'history' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function scopeWherePending($query, $user_id)
    {
        return $query->where('user_id', $user_id)
            ->whereNotIn('status' , ['CANCELLED', 'COMPLETED']);
    }
}
