<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;
use App\Traits\Reorderable;

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
