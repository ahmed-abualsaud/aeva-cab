<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $guarded = [];
    
	public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class)
            ->select('id', 'name', 'phone', 'avatar');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class)
            ->select('id', 'name');
    }
}
