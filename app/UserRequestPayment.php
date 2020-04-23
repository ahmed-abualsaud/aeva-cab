<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRequestPayment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'status', 'password', 'created_at', 'updated_at'
    ];

    public function request()
    {
        return $this->belongsTo('App\UserRequest');
    } 

    public function driver()
    {
        return $this->belongsTo('App\Driver');
    }
}
