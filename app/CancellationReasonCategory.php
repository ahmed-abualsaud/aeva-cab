<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CancellationReasonCategory extends Model
{
    protected $guarded = [];

    public function reasons()
    {
        return $this->hasMany(CancellationReason::class, 'category_id');
    }
}
