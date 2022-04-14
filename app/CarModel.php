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

    protected static $flushCacheOnUpdate = true;

    public function type()
    {
        return $this->belongsTo(CarType::class);
    }

    public function make()
    {
        return $this->belongsTo(CarMake::class);
    }

    public function scopeIsPublic($query, $args) 
    {
        if (array_key_exists('is_public', $args) && $args['is_public']) {
            return $query->where('is_public', true);
        }
        
        return $query;
    }

    public static function reorder(array $orders)
    {
        self::flushQueryCache();

        return self::handleReorder(
            (new self())->getTable(),
            $orders
        );
    }
}
