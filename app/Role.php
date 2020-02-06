<?php

namespace App;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Role extends Authenticatable
{
    use HasApiTokens;
    use Notifiable; 

    protected $guarded = [];

    protected $hidden = ['password'];

    public function role_type()
    {
        return $this->belongsTo(RoleType::class);
    }
}
