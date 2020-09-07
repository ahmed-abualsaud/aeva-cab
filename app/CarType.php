<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rennokki\QueryCache\Traits\QueryCacheable;

class CarType extends Model
{
    use SoftDeletes;
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

    public function scopeFilter($query, $args) 
    {
        if (array_key_exists('ondemand', $args) && !$args['ondemand']) {
            return $query;
        }

        return $query->where('ondemand', true);;
    }
}
