<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerUser extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function scopeGetIds($query, array $args)
    {
        return $query->select('user_id')
            ->where('partner_id', $args['partner_id']);
    }
}
