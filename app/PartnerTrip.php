<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerTrip extends Model
{
    protected $guarded = [];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function stations()
    {
        return $this->hasMany(PartnerTripStation::class);
    }

    public function subbedUsers()
    {
        return $this->belongsToMany(PartnerUser::class, 'partner_trip_users');
    }
}
