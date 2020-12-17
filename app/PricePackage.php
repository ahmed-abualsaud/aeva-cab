<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PricePackage extends Model
{
    protected $guarded = [];

    protected $casts = [
        'price' => 'array',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function scopeFilter($query, $args) 
    {
        if (array_key_exists('city_id', $args) && $args['city_id']) {
            return $query->where('city_id', $args['city_id']);
        }
 
        return $query;
    }

}
