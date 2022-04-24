<?php

namespace Aeva\Seats\Domain\Models;

use App\Driver;
use App\Partner;
use App\Vehicle;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeatsTrip extends Model
{
    use Searchable;
    use SoftDeletes;
    
    protected $guarded = [];

    protected $casts = [
        'days' => 'array'
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function line() 
    {        
        return $this->belongsTo(SeatsLine::class);
    }

    public function stations() 
    {        
        return $this->hasMany(SeatsLineStation::class, 'line_id', 'line_id');
    }

    public function scopeLive($query) 
    {
        return $query->whereNotNull('log_id')->whereNotNull('starts_at');
    }

    public function scopePartner($query, $args) 
    {
        if (array_key_exists('partner_id', $args) && $args['partner_id'])
            $query->where('partner_id', $args['partner_id']);
        
 
        return $query;
    }

    public function scopeUnready($query) 
    {
        $day = strtolower(date('l'));

        return $query->whereNull('ready_at')
            ->whereRaw('? between start_date and end_date', [date('Y-m-d')])
            ->whereRaw('days->"$.'.$day.'" <> CAST("null" AS JSON)')
            ->whereRaw('TIME_TO_SEC(?) between TIME_TO_SEC(days->"$.'.$day.'") - 60*30 
                and TIME_TO_SEC(days->"$.'.$day.'")', [date('H:i:s')]);
    }

    public function scopeSearch($query, $args) 
    {
        if (array_key_exists('searchQuery', $args) && $args['searchQuery'])
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);

        return $query->latest();
    }
    
}

