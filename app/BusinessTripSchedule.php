<?php

namespace App;

use App\Traits\HandleUpdateOrInsert;
use Illuminate\Database\Eloquent\Model;

class BusinessTripSchedule extends Model
{
    use HandleUpdateOrInsert;

    protected $guarded = [];

    protected $primaryKey = ['trip_id', 'user_id'];

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'days' => 'array'
    ];

    public static function upsert(array $rows, array $update)
    {
        return self::updateOrInsert(
            (new self())->getTable(),
            $rows,
            $update
        );
    }

}
