<?php

namespace Qruz\Seats\Domain\Models;

use App\Scopes\SortByOrderScope;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeatsLineStation extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SortByOrderScope);
    }
}
