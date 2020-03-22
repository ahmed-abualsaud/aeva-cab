<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    protected $guarded = [];

    public function tokenable()
    {
        return $this->morphTo();
    }
}
