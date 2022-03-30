<?php

namespace App;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeatsLine extends Model
{
    use Searchable;
    use SoftDeletes;
    
    protected $guarded = [];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function stations() 
    {        
        return $this->hasMany(SeatsLineStation::class, 'line_id');
    }

    public function scopePartner($query, $args) 
    {
        if (array_key_exists('partner_id', $args) && $args['partner_id'])
            $query->where('partner_id', $args['partner_id']);
        
 
        return $query;
    }

    public function scopeSearch($query, $args) 
    {
        if (array_key_exists('searchQuery', $args) && $args['searchQuery'])
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);

        return $query->latest();
    }

    public function scopeZone($query, $args) 
    {
        if (array_key_exists('zone_id', $args) && $args['zone_id']) {
            return $query->where('zone_id', $args['zone_id']);
        }
 
        return $query;
    }
}
