<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class OndemandRequestVehicle extends Model
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

    public function carType()
    {
        return $this->belongsTo(CarType::class, 'car_type_id');
    }

    public function carModel()
    {
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }
}
