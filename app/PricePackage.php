<?php

namespace App;

use App\Traits\Reorderable;
use Illuminate\Database\Eloquent\Model;

class PricePackage extends Model
{
    use Reorderable;

    protected $guarded = [];

    protected $casts = [
        'price' => 'array',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function scopeWhereType($query, $args) 
    {
        if (array_key_exists('type', $args) && $args['type']) {
            return $query->where('type', $args['type']);
        }
 
        return $query;
    }

    public function scopeWhereCity($query, $args) 
    {
        if (array_key_exists('city_id', $args) && $args['city_id']) {
            return $query->where('city_id', $args['city_id'])
                ->orderBy('order');
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
