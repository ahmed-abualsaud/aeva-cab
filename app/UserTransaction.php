<?php

namespace App;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class UserTransaction extends Model
{
    use Searchable, Filterable;

    protected $guarded = [];
    
	public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class)
            ->select('id', 'name', 'phone', 'avatar');
    }

    public function admin()
    {
        return $this->morphTo();
    }

    public function scopeSearch($query, $args) 
    {
        if (array_key_exists('searchQuery', $args) && $args['searchQuery']) {
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);
        }

        return $query;
    }

    public function scopePeriod($query, $args)
    {
        if (array_key_exists('period', $args) && $args['period']) {
            $query = $this->dateFilter($args['period'], $query, 'created_at');
        }

        return $query->latest();
    }

    public function scopePartner($query, $args) 
    {
        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            return $query->where('partner_id', $args['partner_id']);
        }
 
        return $query;
    }

    public function scopeService($query, $args) 
    {
        if (array_key_exists('service', $args) && $args['service']) {
            return $query->where('service', $args['service']);
        }
 
        return $query;
    }
}
