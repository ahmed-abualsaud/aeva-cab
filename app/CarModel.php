<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class CarModel extends Model
{
    use QueryCacheable;
    
    protected $guarded = [];

    public $cacheFor = 3600;

    /**
     * Invalidate the cache automatically
     * upon update in the database.
     *
     * @var bool
     */
    protected static $flushCacheOnUpdate = true;

    public function type()
    {
        return $this->belongsTo(CarType::class);
    }

    public function make()
    {
        return $this->belongsTo(CarMake::class);
    }
}
