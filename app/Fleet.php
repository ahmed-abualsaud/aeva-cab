<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fleet extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }
}
