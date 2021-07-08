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

    public function scopeOfType($query, $args) 
    {
        if (array_key_exists('type', $args) && $args['type']) {
            return $query->where('type', $args['type']);
        }
 
        return $query->where('type', 'TOSCHOOL');
    }

    public function scopeIsPublic($query, $args) 
    {
        if (array_key_exists('is_public', $args) && $args['is_public']) {
            return $query->where('is_public', true);
        }
        
        return $query;
    }

    public function scopeCity($query, $args) 
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
            $orders
        );
    }

}
