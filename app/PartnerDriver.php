<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerDriver extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function scopeByPartner($query, $args)
    {
        return $query->select('driver_id')
            ->where('partner_id', $args['partner_id'])
            ->pluck('driver_id');
    }
}
