<?php

namespace App;

use App\Traits\Reorderable;
use App\Scopes\SortByOrderScope;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class CarModel extends Model
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

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SortByOrderScope);
    }

    public function type()
    {
        return $this->belongsTo(CarType::class);
    }

    public function make()
    {
        return $this->belongsTo(CarMake::class);
    }

    public function scopeSortByOrder($query) 
    {
        return $query->orderBy('order');
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
