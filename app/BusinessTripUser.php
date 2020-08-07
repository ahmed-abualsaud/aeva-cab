<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class BusinessTripUser extends Model
{ 
    protected $guarded = [];

    protected $primaryKey = ['trip_id', 'user_id'];

    public $incrementing = false;

    /**
     * Set the keys for a save update query.
     * This is a fix for tables with composite keys
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery(Builder $query)
    {
        $query
            ->where('trip_id', '=', $this->trip_id)
            ->where('user_id', '=', $this->user_id);

        return $query;
    }
    
} 
