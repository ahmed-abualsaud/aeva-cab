<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fleet extends Model
{
    
    protected $guarded = [];

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords($value);
    }
}
