<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = [];

    public function role_type()
    {
        return $this->belongsTo(RoleType::class);
    }
}
