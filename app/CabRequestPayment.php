<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CabRequestPayment extends Model
{

    protected $guarded = [];
    
    public function request()
    {
        return $this->belongsTo('App\CabRequest');
    }

}
