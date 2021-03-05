<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;
use App\Traits\Reorderable;

class CarType extends Model
{
    use QueryCacheable;
    use Reorderable;
    
    protected $guarded = [];

    public $cacheFor = 3600;

    /**
     * Invalidate the cache automatically
     * upon update in the database.
     *
     * @var bool
     */
    protected static $flushCacheOnUpdate = true;

    public function scopeWhereOndemand($query, $args) 
    {
        if (array_key_exists('ondemand', $args) && !$args['ondemand']) {
            return $query;
        }

        return $query->where('ondemand', true)
            ->orderBy('order');
    }

    public static function reorder(array $orders)
    {
        self::flushQueryCache();
        
        return self::handleReorder(
            (new self())->getTable(),
            $orders,
        );
    }
}
