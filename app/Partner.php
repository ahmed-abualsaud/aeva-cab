<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $guarded = [];

    protected $hidden = ['password'];

    public function users()
    {
        return $this->hasMany(PartnerUser::class);
    }

    public function drivers()
    {
        return $this->belongsToMany(Driver::class, 'partner_drivers');
    }

    public function trips()
    {
        return $this->hasMany(PartnerTrip::class);
    }
}
