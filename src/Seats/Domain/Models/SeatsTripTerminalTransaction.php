<?php

namespace Qruz\Seats\Domain\Models;

use App\Partner;
use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class SeatsTripTerminalTransaction extends Model
{
    use Filterable;
    use Searchable;

    protected $guarded = [];
    
	public $timestamps = false;

    public function scopeSearch($query, $args) 
    {
        if (array_key_exists('searchQuery', $args) && $args['searchQuery']) {
            $query = $this->search($args['searchFor'], $args['searchQuery'], $query);
        }

        return $query;
    }

    public function scopePeriod($query, $args)
    {
        if (array_key_exists('period', $args) && $args['period']) {
            $query = $this->dateFilter($args['period'], $query, 'created_at');
        }

        return $query->latest();
    }

    public function scopePartner($query, $args) 
    {
        if (array_key_exists('partner_id', $args) && $args['partner_id']) {
            return $query->where('partner_id', Partner::getPaymobID($args['partner_id']));
        }
 
        return $query;
    }

    public function scopeTerminal($query, $args) 
    {
        if (array_key_exists('terminal_id', $args) && $args['terminal_id']) {
            return $query->where('terminal_id', $args['terminal_id']);
        }
 
        return $query;
    }
}
