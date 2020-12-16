<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $guarded = [];

    public function zone()
    {
        return $this->belongsTo(SchoolZone::class);
    }

    public function grades()
    {
        return $this->hasMany(SchoolGrade::class);
    }

    public function scopeFilter($query, $args) 
    {
        if (array_key_exists('zone_id', $args) && $args['zone_id']) {
            return $query->where('zone_id', $args['zone_id']);
        }

        if (array_key_exists('city_id', $args) && $args['city_id']) {
            return $query->whereHas('zone', function($query) use ($args) {
                $query->where('city_id', $args['city_id']);
            });
        }
 
        return $query;
    }
}
