<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Terminal extends Model
{
    protected $guarded = [];

    public function partner()
    {
        return $this->belongsTo(Partner::class)
            ->select('id', 'name');
    }
}
