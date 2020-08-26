<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class OndemandRequestLine extends Model
{
    use QueryCacheable;

    public $cacheFor = 3600;

    /**
     * Invalidate the cache automatically
     * upon update in the database.
     *
     * @var bool
     */
    protected static $flushCacheOnUpdate = true;
    
    protected $guarded = [];

    public $timestamps = false;
}
