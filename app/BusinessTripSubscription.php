<?php

namespace App;

use App\Traits\HandleUpdateOrInsert;
use Illuminate\Database\Eloquent\Model;

class BusinessTripSubscription extends Model
{ 
    use HandleUpdateOrInsert;

    protected $guarded = [];

    public $table = 'business_trip_users';

    public function trip()
    {
        return $this->belongsTo(BusinessTrip::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)
            ->select('id', 'name', 'phone', 'avatar');
    }

    public function pickup()
    {
        return $this->belongsTo(BusinessTripStation::class, 'station_id');
    }

    public function dropoff()
    {
        return $this->belongsTo(BusinessTripStation::class, 'destination_id');
    }

    public static function upsert(array $rows, array $update)
    {
        return self::updateOrInsert(
            (new self())->getTable(),
            $rows,
            $update
        );
    }
} 
