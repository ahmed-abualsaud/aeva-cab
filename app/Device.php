<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $guarded = [];

    protected $casts = [
        'attributes' => 'array'
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class)
            ->select('id', 'name');
    }

    public function scopePartner($query, $args) 
    {
        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            return $query->where('partner_id', $args['partner_id']);
        }
 
        return $query;
    }
}
