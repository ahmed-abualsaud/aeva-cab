<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessTripEvent extends Model
{
    
    protected $guarded = [];

    protected $primaryKey = 'log_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = [
        'content' => 'json',
    ];

    public function trip()
    {
        return $this->belongsTo(BusinessTrip::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class)->select('id', 'name');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class)->select('id', 'license_plate');
    }
}
