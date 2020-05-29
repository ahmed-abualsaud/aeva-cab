<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
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
        'created_at', 'updated_at'
    ];

    /**
     * The user who created the request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The driver assigned to the request.
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Get the owning ratingable model.
     */
    public function ratingable()
    {
        return $this->morphTo();
    }
}
