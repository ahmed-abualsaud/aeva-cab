<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    
    /**
     * Get the owning documentable model.
     */
    public function documentable()
    {
        return $this->setConnection('mysql')->morphTo();
    }

    public function admin()
    {
        return $this->setConnection('mysql2')->belongsTo(Admin::class);
    }
}