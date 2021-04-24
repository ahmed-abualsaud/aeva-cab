<?php

namespace App;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class SeatsTrip extends Model
{
    use Searchable;
    
    protected $guarded = [];

    protected $casts = [
        'days' => 'array'
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function line() 
    {        
        return $this->belongsTo(SeatsLine::class);
    }

    public function stations() 
    {        
        return $this->hasMany(SeatsLineStation::class, 'line_id', 'line_id');
    }

    public function scopeLive($query) 
    {
        return $query->whereNotNull('log_id');
    }

    public function scopePartner($query, $args) 
    {
        if (array_key_exists('partner_id', $args) && $args['partner_id'])
            $query->where('partner_id', $args['partner_id']);
        
 
        return $query->latest();
    }

    public function scopeSearch($query, $args) 
    {
        if (array_key_exists('searchQuery', $args) && $args['searchQuery'])
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);

        return $query;
    }
    
}

