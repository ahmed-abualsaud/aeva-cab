<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverStats extends Model
{
    protected $guarded = [];

    protected $appends = [
        'acceptance_rate',
        'cancellation_rate',
        'missing_rate'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function getAcceptanceRateAttribute()
    {
        if ($this->missed_cab_requests == 0) {return 0;}
        return ($this->accepted_cab_requests / $this->missed_cab_requests);
    }

    public function getCancellationRateAttribute()
    {
        if ($this->accepted_cab_requests == 0) {return 0;}
        return ($this->cancelled_cab_requests / $this->accepted_cab_requests);
    }

    public function getMissingRateAttribute()
    {
        if ($this->received_cab_requests == 0) {return 0;}
        return ($this->missed_cab_requests / $this->received_cab_requests);
    }
}
