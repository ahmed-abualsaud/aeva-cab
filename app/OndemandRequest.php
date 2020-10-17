<?php

namespace App;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class OndemandRequest extends Model
{ 
    use Filterable;
    
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicles()
    {
        return $this->hasMany(OndemandRequestVehicle::class, 'request_id');
    }

    public function lines()
    {
        return $this->hasMany(OndemandRequestLine::class, 'request_id');
    }

    public function scopeFilter($query, $args) 
    {
        
        if (array_key_exists('status', $args) && $args['status']) {
            $query->where('status', $args['status']);
        }

        if (array_key_exists('period', $args) && $args['period']) {
            $query = $this->dateFilter($args['period'], $query, 'created_at');
        }

        return $query->orderBy('created_at', 'DESC');
    }
}
