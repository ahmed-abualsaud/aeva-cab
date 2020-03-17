<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $guarded = [];
    
    /**
     * Get the owning documentable model.
     */
    public function documentable()
    {
        return $this->morphTo();
    }
}