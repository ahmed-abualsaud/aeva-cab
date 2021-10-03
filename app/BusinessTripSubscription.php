<?php

namespace App;

use App\Traits\Searchable;
use App\Traits\HandleUpdateOrInsert;
use Illuminate\Database\Eloquent\Model;

class BusinessTripSubscription extends Model
{ 
    use HandleUpdateOrInsert;
    use Searchable;

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
    
    public function getContentAttribute($value) {
        return json_decode($value);
    }

    public function scopePartner($query, $args)
    {
        if (array_key_exists('partner_id', $args) && $args['partner_id'])
            return $query->whereHas('trip', function($query) use ($args) {
                $query->where('partner_id', $args['partner_id']);
            });

        return $query;
    }

    public function scopeSearch($query, $args) 
    {
        if (array_key_exists('searchQuery', $args) && $args['searchQuery'])
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);

        return $query->latest();
    }

    public function scopeShouldRenew($query, $args) 
    {
        if (array_key_exists('shouldRenew', $args) && $args['shouldRenew'])
            return $query->whereDate('due_date', '<=', date('Y-m-d'));

        return $query;
    }

    public function scopeOfType($query, $args) 
    {
        if (array_key_exists('type', $args) && $args['type'])
            return $query->whereHas('trip', function($query) use ($args) {
                $query->where('type', $args['type']);
            });
 
        return $query;
    }
} 
