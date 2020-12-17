<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolRequest extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function grade()
    {
        return $this->belongsTo(SchoolGrade::class);
    }

    public function pricePackage()
    {
        return $this->belongsTo(PricePackage::class);
    }

    public function scopeZone($query, $args) 
    {
        if (array_key_exists('zone_id', $args) && $args['zone_id']) {
            return $query->whereHas('school', function($query) use ($args) {
                $query->whereIn('zone_id', $args['zone_id']);
            });
        }
 
        return $query->orderBy('created_at', 'DESC');
    }
}
