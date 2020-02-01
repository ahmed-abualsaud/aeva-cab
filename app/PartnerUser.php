<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerUser extends Model
{
    protected $guarded = [];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
