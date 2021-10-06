<?php

namespace App;

use App\DriverVehicle;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use Searchable;
    
    protected $guarded = [];

    public function make()
    {
        return $this->belongsTo(CarMake::class, 'car_make_id');
    }

    public function model()
    {
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }

    public function type()
    {
        return $this->belongsTo(CarType::class, 'car_type_id');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function scopeAssigned($query, $args) 
    {
        return $query->whereIn('id', DriverVehicle::getIds($args));
    }

    public function scopeNotAssigned($query, $args) 
    {
        return $query->whereNotIn('id', DriverVehicle::getIds($args));
    }

    public function scopePartner($query, $args) 
    {
        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            return $query->where('partner_id', $args['partner_id']);
        }
 
        return $query;
    }

    public function scopeHaveTerminal($query) 
    {
        return $query->select('terminal_id', 'license_plate', 'code')
            ->whereNotNull('terminal_id');
    }

    public function scopeSearch($query, $args) 
    {
        if (array_key_exists('searchQuery', $args) && $args['searchQuery']) {
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);
        }

        return $query;
    }
}
