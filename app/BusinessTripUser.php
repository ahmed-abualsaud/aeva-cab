<?php

namespace App;

use App\Traits\HandleUpdateOrInsert;
use Illuminate\Database\Eloquent\Model;

class BusinessTripUser extends Model
{ 
    use HandleUpdateOrInsert;

    protected $guarded = [];

    public static function upsert(array $rows, array $update)
    {
        return self::updateOrInsert(
            (new self())->getTable(),
            $rows,
            $update
        );
    }
} 
