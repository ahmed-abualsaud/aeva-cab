<?php

namespace App;

use App\Scopes\SortByOrderScope;
use Illuminate\Database\Eloquent\Model;

class SeatsTripStation extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SortByOrderScope);
    }
}
