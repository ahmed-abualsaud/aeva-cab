<?php

namespace App;

use App\Traits\DateFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rennokki\QueryCache\Traits\QueryCacheable;

class OndemandRequest extends Model
{ 
    use SoftDeletes;
    use DateFilter;
    // use QueryCacheable;
    
    protected $guarded = [];

    // public $cacheFor = 3600;

    /**
     * Invalidate the cache automatically
     * upon update in the database.
     *
     * @var bool
     */
    // protected static $flushCacheOnUpdate = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicles()
    {
        return $this->hasMany(OndemandRequestVehicle::class, 'request_id');
    }

    public function lines()
    {
        return $this->hasMany(OndemandRequestLine::class, 'request_id');
    }

    public function scopeFilter($query, $args) 
    {
        
        if (array_key_exists('status', $args) && $args['status']) {
            $query->where('status', $args['status']);
        }

        if (array_key_exists('period', $args) && $args['period']) {
            $query = $this->dateFilter($args['period'], $query, 'created_at');
        }

        return $query->orderBy('created_at', 'DESC');
    }
}
