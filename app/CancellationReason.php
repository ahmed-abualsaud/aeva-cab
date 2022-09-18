<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CancellationReason extends Model
{
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(CancellationReasonCategory::class);
    }
}
