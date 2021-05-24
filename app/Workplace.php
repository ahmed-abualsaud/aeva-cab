<?php

namespace App;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Workplace extends Model
{
    use Searchable;

    protected $guarded = [];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function scopeZone($query, $args) 
    {
        if (array_key_exists('zone_id', $args) && $args['zone_id']) {
            return $query->where('zone_id', $args['zone_id']);
        }
 
        return $query;
    }

    public function scopeCity($query, $args) 
    {
        if (array_key_exists('city_id', $args) && $args['city_id']) {
            return $query->whereHas('zone', function($query) use ($args) {
                $query->where('city_id', $args['city_id']);
            });
        }
 
        return $query;
    }

    public function scopeSearch($query, $args) 
    {
        if (array_key_exists('searchQuery', $args) && $args['searchQuery']) {
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);
        }

        return $query->orderBy('zone_id');
    }
}
