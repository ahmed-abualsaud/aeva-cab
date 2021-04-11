<?php

namespace App;

use App\Traits\Reorderable;
use App\Scopes\SortByOrderScope;
use Illuminate\Database\Eloquent\Model;

class PricePackage extends Model
{
    use Reorderable;

    protected $guarded = [];

    protected $casts = [
        'price' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SortByOrderScope);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function scopeWhereType($query, $args) 
    {
        if (array_key_exists('type', $args) && $args['type']) {
            return $query->where('type', $args['type']);
        }
 
        return $query->where('type', 'toschool');
    }

    public function scopeWhereCity($query, $args) 
    {
        if (array_key_exists('city_id', $args) && $args['city_id']) {
            return $query->where('city_id', $args['city_id']);
        }
 
        return $query;
    }

    public static function reorder(array $orders)
    {
        return self::handleReorder(
            (new self())->getTable(),
            $orders,
        );
    }

}
